<?php

namespace Ravuthz\LaravelCrud;

trait HasTestCrud
{
    public int $idOk = 1;
    public int $idFail = 9999;
    abstract function requestPayload($id = null): array;
    public function requestRoute($id = null): string
    {
        abort_if(!isset($this->route), 500, 'Route not defined');
        return $this->route . '/' . ($id ?? '');
    }
    public function test_index(): void
    {
        $res = $this->getJson($this->requestRoute());
        $res->assertStatus(200);
    }
    public function test_store(): void
    {
        $res = $this->postJson($this->requestRoute(), $this->requestPayload());
        $res->assertStatus(200);
    }
    public function test_show(): void
    {
        $res = $this->getJson($this->requestRoute($this->idOk));
        $res->assertStatus(200);
    }
    public function test_show_not_found(): void
    {
        $res = $this->getJson($this->requestRoute($this->idFail));
        $res->assertStatus(404);
    }

    public function test_update(): void
    {
        $res = $this->patchJson($this->requestRoute($this->idOk), $this->requestPayload($this->idOk));
        $res->assertStatus(200);
    }

    public function test_update_not_found(): void
    {
        $res = $this->patchJson($this->requestRoute($this->idFail), $this->requestPayload($this->idFail));
        $res->assertStatus(404);
    }

    public function test_destroy(): void
    {
        $res = $this->deleteJson($this->requestRoute($this->idOk));
        $res->assertStatus(200);
    }

    public function test_destroy_not_found(): void
    {
        $res = $this->deleteJson($this->requestRoute($this->idFail));
        $res->assertStatus(404);
    }
}
