<?php

namespace Ravuthz\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrudControllerCommand extends Command
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
    protected $description = 'Generate a new CRUD controller.';

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

        $template = str_replace(
            '{{model}}',
            $name,
            file_get_contents($this->getStub())
        );

        $appPath = "Http/Controllers/Api/{$name}Controller.php";

        $file = app_path($appPath);

        file_put_contents($file, $template);

        $this->info("Controller [$file] created successfully.");

        $routePath = base_path("routes/api.php");

        $content = file_get_contents($routePath);
        $content .= "\n\nRoute::apiResource('" . Str::of($name)->plural()->snake()->slug();
        $content .= "', App\\Http\\Controllers\\Api\\{$name}Controller::class)";
        $content .= "\n\t->middleware('auth:api');";

        file_put_contents($routePath, $content);

        $this->info("Route API was updated successfully . ");
    }
}
