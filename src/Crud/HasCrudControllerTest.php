<?php

namespace Ravuthz\LaravelCrud\Crud;

trait HasCrudControllerTest
{
    protected static bool $running = false;
    protected int $idOk = 1;
    protected int $idFail = 9999;
    protected function onceSetUp(): void
    {
        if (!static::$running) {
            static::$running = true;
            $this->artisan('migrate:fresh');
        }
    }

    public function test_index(): void
    {
        $res = $this->getJson($this->route . '/');
        $res->assertStatus(200);
    }

    public function test_store(): void
    {
        $res = $this->postJson($this->route . '/', $this->inputBody());
        $res->assertStatus(200);
    }

    public function test_show(): void
    {
        $res = $this->getJson($this->route . '/' . $this->idOk);
        $res->assertStatus(200);
    }

    public function test_show_not_found(): void
    {
        $res = $this->getJson($this->route . '/' . $this->idFail);
        $res->assertStatus(404);
    }

    public function test_update(): void
    {
        $res = $this->patchJson($this->route . '/' . $this->idOk, $this->inputBody($this->idOk));
        $res->assertStatus(200);
    }

    public function test_update_not_found(): void
    {
        $res = $this->patchJson($this->route . '/' . $this->idFail, $this->inputBody($this->idOk));
        $res->assertStatus(404);
    }

    public function test_destroy(): void
    {
        $res = $this->deleteJson($this->route . '/' . $this->idOk);
        $res->assertStatus(200);
    }

    public function test_destroy_not_found(): void
    {
        $res = $this->deleteJson($this->route . '/' . $this->idFail);
        $res->assertStatus(404);
    }
}
