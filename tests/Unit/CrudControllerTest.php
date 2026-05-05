<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;
use Ravuthz\LaravelCrud\CrudController;

class CrudControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container();
        Container::setInstance($app);
        Facade::setFacadeApplication($app);

        $app->instance('app', $app);
        $app->bind(FakeControllerModel::class, fn () => new FakeControllerModel());
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_response_json_returns_expected_payload(): void
    {
        $response = (new TestCrudController())->json(
            ['title' => 'Post'],
            201,
            'Created',
            ['meta' => ['page' => 1]]
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'meta' => ['page' => 1],
            'data' => ['title' => 'Post'],
            'status' => 201,
            'message' => 'Created',
        ], $response->getData(true));
    }

    public function test_store_uses_incoming_request_when_no_form_request_is_configured(): void
    {
        $request = Request::create('/posts', 'POST', ['title' => 'Post']);

        $response = (new TestCrudController())->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Created', $response->getData(true)['message']);
        $this->assertSame('Post', $response->getData(true)['data']['title']);
    }

    public function test_update_uses_incoming_request_when_no_form_request_is_configured(): void
    {
        $request = Request::create('/posts/10', 'PATCH', ['title' => 'Updated']);

        $response = (new TestCrudController())->update($request, '10');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Updated', $response->getData(true)['message']);
        $this->assertSame('Updated', $response->getData(true)['data']['title']);
    }
}

class TestCrudController extends CrudController
{
    protected $model = FakeControllerModel::class;

    public function json($data, $status = null, $message = null, $extra = []): JsonResponse
    {
        return $this->responseJson($data, $status, $message, $extra);
    }
}

class FakeControllerModel extends Model
{
    protected $fillable = ['title'];

    public $timestamps = false;

    public function save(array $options = []): bool
    {
        return true;
    }

    public function findOne(string $id): self
    {
        $model = new self();
        $model->setAttribute($model->getKeyName(), $id);

        return $model;
    }
}
