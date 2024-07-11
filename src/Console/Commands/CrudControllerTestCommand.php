<?php

namespace Ravuthz\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrudControllerTestCommand extends Command
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
    protected $description = 'Generate a new CRUD controller test.';

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

        $template = str_replace(
            '{{model}}',
            $name,
            file_get_contents($this->getStub())
        );

        $template = str_replace(
            '{{route}}',
            Str::of($name)->plural()->snake()->slug(),
            $template
        );

        $path = "tests/Feature/Http/Controllers/Api/{$name}ControllerTest.php";

        $file = base_path($path);

        file_put_contents($file, $template);

        $this->info("\nTest Controller [$path] created successfully.");
    }
}
