<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use LogicException;
use PHPUnit\Framework\TestCase;
use Ravuthz\LaravelCrud\CrudService;

class CrudServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container();
        Container::setInstance($app);
        Facade::setFacadeApplication($app);

        $app->instance('app', $app);
        $app->bind(FakeCrudModel::class, fn () => new FakeCrudModel());
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_get_model_requires_configured_model(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('CRUD model is not configured.');

        (new CrudService())->getModel();
    }

    public function test_save_from_request_fills_model_saves_and_runs_callbacks(): void
    {
        $events = [];
        $request = Request::create('/posts', 'POST', ['title' => 'Draft']);
        $service = new CrudService(FakeCrudModel::class);

        $model = $service->saveFromRequest(
            $request,
            null,
            function ($request, $model, $id) use (&$events) {
                $events[] = ['before', $request->input('title'), $id];
                $model->title = 'Published';
            },
            function ($request, $model, $id) use (&$events) {
                $events[] = ['after', $model->title, $id];
            }
        );

        $this->assertTrue($model->wasSaved);
        $this->assertSame('Published', $model->title);
        $this->assertSame([
            ['before', 'Draft', null],
            ['after', 'Published', null],
        ], $events);
    }

    public function test_find_methods_delegate_to_custom_model_methods(): void
    {
        $request = Request::create('/posts', 'GET', ['size' => 25]);
        $service = new CrudService(FakeCrudModel::class);

        $this->assertSame(5, $service->findOne('5')->getKey());
        $this->assertSame(['size' => 25], $service->findAll($request));
    }
}

class FakeCrudModel extends Model
{
    protected $fillable = ['title'];

    public $timestamps = false;
    public bool $wasSaved = false;

    public function save(array $options = []): bool
    {
        $this->wasSaved = true;

        return true;
    }

    public function findOne(string $id): self
    {
        $model = new self();
        $model->setAttribute($model->getKeyName(), $id);

        return $model;
    }

    public function findAll($request): array
    {
        return ['size' => (int) $request->get('size')];
    }
}
