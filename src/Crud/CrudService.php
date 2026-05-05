<?php

namespace Ravuthz\LaravelCrud;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use LogicException;

class CrudService
{
    private ?Model $model = null;
    private $beforeSaveFn = null;
    private $afterSaveFn = null;

    public function __construct($model = null)
    {
        if ($model) {
            $this->setModel($model);
        }
    }

    public function setModel($model): CrudService
    {
        $this->model = $this->resolveModel($model);
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
     */
    public function getModel(): Model
    {
        if (!$this->model) {
            throw new LogicException('CRUD model is not configured.');
        }

        return $this->model;
    }

    /**
     * @throws \Exception
     */
    public function findOne(string $id)
    {
        if (method_exists($this->getModel(), 'findOne')) {
            return $this->getModel()->findOne($id);
        }
        return $this->getModel()->findOrFail($id);
    }

    /**
     * @throws \Exception
     */
    public function findAll($request)
    {
        if (method_exists($this->getModel(), 'findAll')) {
            return $this->getModel()->findAll($request);
        }
        return $this->getModel()->paginate($request->get('size', 10));
    }

    /**
     * @throws \Exception
     */
    public function delete(string $id)
    {
        $this->findOne($id)->delete();
        return null;
    }

    /**
     * @throws \Exception
     */
    public function save($request, ?string $id = null)
    {
        return $this->saveFromRequest($request, $id, $this->beforeSaveFn, $this->afterSaveFn);
    }

    public function saveFromRequest($request, ?string $id = null, $beforeSaveFn = null, $afterSaveFn = null)
    {
        $model = $this->fillModel(array_merge(['id' => $id], $request->all()));

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

    private function resolveModel($model): Model
    {
        $resolvedModel = $model instanceof Model ? $model : App::make($model);

        if (!$resolvedModel instanceof Model) {
            throw new InvalidArgumentException('CRUD model must be an Eloquent model instance or class name.');
        }

        return $resolvedModel;
    }
}
