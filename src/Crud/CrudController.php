<?php

namespace Ravuthz\LaravelCrud;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

abstract class CrudController extends Controller
{
    protected $model = null;
    protected $resource = null;

    protected $collection = null;
    protected $storeRequest = null;
    protected $updateRequest = null;

    protected CrudService $service;

    public function __construct()
    {
        $this->service = new CrudService($this->model);
        $this->service
            ->setBeforeSave(fn(...$args) => $this->beforeSave(...$args))
            ->setAfterSave(fn(...$args) => $this->afterSave(...$args));
    }

    protected function beforeSave($request, $model, $id = null)
    {
        return $model;
    }

    protected function afterSave($request, $model, $id = null)
    {
        return $model;
    }

    protected function responseJson($data, $status = null, $message = null, $extra = [])
    {
        $status = $status ?? 200;
        return response()->json([
            ...$extra,
            'data' => $data ?? [],
            'status' => $status,
            'message' => $message ?? 'Successfully',
        ], $status);
    }

    protected function responseList($data, $status = null, $message = null)
    {
        $extra = [];

        if ($data instanceof Paginator) {
            $extra['meta'] = [
                'size' => $data->perPage(),
                'page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'total_items' => $data->total(),
            ];
        }

        if ($this->resource) {
            $data = $this->resource::collection($data);
        }

        if ($this->collection) {
            $data = $this->collection::make($data);
        }

        if ($data instanceof JsonResource) {
            $result = $data->response()->getData(true);
            $extra['meta'] = [
                'size' => $result['meta']['per_page'],
                'page' => $result['meta']['current_page'],
                'total_pages' => $result['meta']['last_page'],
                'total_items' => $result['meta']['total'],
            ];
        }

        return $this->responseJson($data, $status, $message, $extra);
    }

    protected function responseItem($data, $status = null, $message = null)
    {
        if ($this->resource) {
            $data = $this->resource::make($data);
        }

        return $this->responseJson($data, $status, $message);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->service->findAll($request);
        return $this->responseList($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->service->findOne($id);
        return $this->responseItem($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $result = $this->service->save(app($this->storeRequest) ?? $request);
        return $this->responseItem($result, 200, 'Created');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $result = $this->service->save(app($this->updateRequest) ?? $request, $id);
        return $this->responseItem($result, 200, 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->service->delete($id);
        return $this->responseItem($result, 200, 'Deleted');
    }
}
