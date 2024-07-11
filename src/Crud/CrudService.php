<?php

namespace Ravuthz\LaravelCrud;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CrudService
{
    private Model|null $model;
    private $beforeSaveFn = null;
    private $afterSaveFn = null;

    public function __construct($model = null)
    {
        $this->model = $model ? app($model) : null;
    }

    public function setModel($model): static
    {
        $this->model = app($model);
        return $this;
    }

    /**
     * @return Model
     * @throws Exception
     */
    public function getModel(): Model
    {
        if (!$this->model) {
            throw new Exception("The model is required", 400);
        }
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
        $input = $request->all();
        $model = $id ? $this->findOne($id) : $this->getModel();

        $model->fill($input);

        if (is_callable($this->beforeSaveFn)) {
            call_user_func($this->beforeSaveFn, $request, $model, $id);
        }

        $model->save();

        if (is_callable($this->afterSaveFn)) {
            call_user_func($this->afterSaveFn, $request, $model, $id);
        }

        return $model;
    }

    public function setBeforeSave(callable $callbackFn): static
    {
        $this->beforeSaveFn = $callbackFn;
        return $this;
    }

    public function setAfterSave(callable $callbackFn): static
    {
        $this->afterSaveFn = $callbackFn;
        return $this;
    }
}
