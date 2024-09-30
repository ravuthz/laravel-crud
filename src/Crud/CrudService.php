<?php

namespace Ravuthz\LaravelCrud;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CrudService
{
    private Model|null $model = null;
    private $beforeSaveFn = null;
    private $afterSaveFn = null;

    public function __construct($model = null)
    {
        $this->model = $model ? app($model) : null;
    }

    public function setModel($model): CrudService
    {
        $this->model = app($model);
        return $this;
    }

    public function setBeforeSave(callable $callbackFn): CrudService
    {
        $this->beforeSaveFn = $callbackFn;
        return $this;
    }

    public function setAfterSave(callable $callbackFn): CrudService
    {
        $this->afterSaveFn = $callbackFn;
        return $this;
    }

    /**
     * @return Model
     * @throws Exception
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @throws Exception
     */
    public function findOne(string $id)
    {
        if (method_exists($this->getModel(), 'findOne')) {
            return $this->getModel()->findOne($id);
        }
        return $this->getModel()->findOrFail($id);
    }

    /**
     * @throws Exception
     */
    public function findAll($request)
    {
        if (method_exists($this->getModel(), 'findAll')) {
            return $this->getModel()->findAll($request);
        }
        return $this->getModel()->paginate($request->get('size', 10));
    }

    /**
     * @throws Exception
     */
    public function delete(string $id)
    {
        $this->findOne($id)->delete();
        return null;
    }

    /**
     * @throws Exception
     */
    public function save($request, string $id = null)
    {
        return $this->saveFromRequest($request, $id, $this->beforeSaveFn, $this->afterSaveFn);
    }

    public function saveFromRequest($request, string $id = null, $beforeSaveFn = null, $afterSaveFn = null)
    {
        $model = $this->fillModel([
            'id' => $id,
            ...$request->all()
        ]);

        if (is_callable($beforeSaveFn)) {
            call_user_func($beforeSaveFn, $request, $model, $id);
        }

        $model->save();

        if (is_callable($afterSaveFn)) {
            call_user_func($afterSaveFn, $request, $model, $id);
        }

        return $model;
    }

    public function fillModel(array $input)
    {
        $id = $input['id'] ?? null;
        $model = $id ? $this->findOne($id) : $this->getModel();
        $model->fill($input);
        return $model;
    }

    public function saveModel($input)
    {
        $model = $this->fillModel($input);
        $model->save();
        return $model;
    }
}
