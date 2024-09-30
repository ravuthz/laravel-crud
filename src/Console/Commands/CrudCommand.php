<?php

namespace Ravuthz\LaravelCrud\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ravuthz\LaravelCrud\Crud\Template;

class CrudCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generate {model : The model name} {--test : Generate a test file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CRUD resources for a given model.';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $model = $this->argument('model');

            $this->newLine();
            $this->comment("Generating resources for {$model}...");

            $this->createModel($model);

            $this->createRequest($model);

            $this->createResource($model);

            if ($this->option('test')) {
                $this->call('crud:controller-test', ['name' => $model]);
            }

            $this->call('crud:controller', ['name' => $model]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            exit(1);
        }
    }

    private function createModel($model): void
    {
        $modelTemplate = Template::generate(
            __DIR__ . '/../../stubs/crud.model.stub',
            [
                '{{ namespace }}' => 'App\Models',
                '{{ class }}' => $model,
            ]);

        $this->createTemplate('Model',
            "app/Models/{$model}.php",
            $modelTemplate
        );

        $table = Str::snake(Str::pluralStudly($model));

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    private function createRequest($model): void
    {
        $class = "{$model}Request";
        $template = Template::generate(
            __DIR__ . '/../../stubs/crud.request.stub',
            [
                '{{ namespace }}' => 'App\Http\Requests',
                '{{ class }}' => $class,
            ]);

        $this->createTemplate('Request', "app/Http/Requests/{$class}.php", $template);
    }

    private function createResource($model): void
    {
        $this->call('make:resource', [
            'name' => $model . 'Resource',
        ]);

        $this->call('make:resource', [
            'name' => $model . 'Collection',
            '--collection' => true,
        ]);
    }

}
