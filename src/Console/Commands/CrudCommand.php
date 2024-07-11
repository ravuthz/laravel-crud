<?php

namespace Ravuthz\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;

class CrudCommand extends Command
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
    protected $description = 'Generate CRUD operations for a given model.';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->argument('model');

        $this->call('make:model', ['name' => $model, '--migration' => true]);
        $this->call('make:request', ['name' => $model . 'Request']);
        $this->call('make:resource', ['name' => $model . 'Resource']);
        $this->call('crud:controller', ['name' => $model]);

        if ($this->option('test')) {
            $this->call('crud:controller-test', ['name' => $model]);
        }
    }
}
