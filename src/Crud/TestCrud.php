<?php

namespace Ravuthz\LaravelCrud;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCrud extends TestCase
{
    use WithFaker;
    use HasTestCrud;

    public function refreshLocalDB(): void
    {
//        if .env.testing doesn't exist
//        app('config')->set('app.faker_locale', 'km_KH');
//        app('config')->set('database.default', 'sqlite');
//        app('config')->set('database.connections.sqlite.database', database_path('database.sqlite'));
        file_put_contents(database_path('database.sqlite'), null);
        $this->artisan('migrate:fresh --seed');
    }
}
