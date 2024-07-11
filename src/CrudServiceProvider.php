<?php

namespace Ravuthz\LaravelCrud;

use Illuminate\Support\ServiceProvider;
use Ravuthz\LaravelCrud\Console\Commands\CrudCommand;
use Ravuthz\LaravelCrud\Console\Commands\CrudControllerCommand;
use Ravuthz\LaravelCrud\Console\Commands\CrudControllerTestCommand;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register any bindings or services here
        $this->app->singleton('command.crud.generate', function () {
            return new CrudCommand();
        });

        $this->app->singleton('command.crud.controller', function () {
            return new CrudControllerCommand();
        });

        $this->app->singleton('command.crud.controller-test', function () {
            return new CrudControllerTestCommand();
        });

        $this->commands([
            'command.crud.generate',
            'command.crud.controller',
            'command.crud.controller-test',
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Perform any booting actions here
    }
}
