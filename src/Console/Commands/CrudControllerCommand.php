<?php

namespace Ravuthz\LaravelCrud\Console\Commands;

use Illuminate\Support\Str;
use Ravuthz\LaravelCrud\Crud\Template;

class CrudControllerCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:controller {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new CRUD controller only.';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/crud.controller.stub';
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        $template = Template::generate($this->getStub(), [
            '{{model}}' => $name,
        ]);

        $this->createTemplate('Crud Controller',
            "app/Http/Controllers/Api/{$name}Controller.php",
            $template
        );

        $routePath = "routes/api.php";

        $content = file_get_contents(base_path($routePath));
        $content .= "\n\nRoute::apiResource('" . Str::of($name)->plural()->snake()->slug();
        $content .= "', App\\Http\\Controllers\\Api\\{$name}Controller::class)";
        $content .= "\n\t->middleware('auth:api');";

        $this->updateTemplate('Route', $routePath, $content);

    }
}
