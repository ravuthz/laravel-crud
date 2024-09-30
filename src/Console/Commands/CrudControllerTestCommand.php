<?php

namespace Ravuthz\LaravelCrud\Console\Commands;

use Illuminate\Support\Str;
use Ravuthz\LaravelCrud\Crud\Template;

class CrudControllerTestCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:controller-test {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new CRUD controller test only.';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/crud.controller.test.stub';
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        $template = Template::generate($this->getStub(), [
            '{{model}}' => $name,
            '{{route}}' => Str::of($name)->plural()->snake()->slug(),
        ]);

        $this->createTemplate('Test Controller',
            "tests/Feature/Http/Controllers/Api/{$name}ControllerTest.php",
            $template
        );
    }
}
