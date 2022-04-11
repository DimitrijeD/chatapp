<?php

namespace App\ChatApp\General\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait CRUDTrait
{
    public function find($id)
    {
        return $this->getModel()::find($id);
    }

    public function create(array $data)
    {
        $instance = app()->make($this->getModel());

        foreach ($data as $attribute => $value) {
            $instance->$attribute = $value;
        }

        if (! $instance->save()) {
            throw new \Exception(__('Model not created'));
        }

        return $instance;
    }

    public function update(Model $model, array $data)
    {
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        $model->save();
    
        return $model;
    }

    public function delete(Model $model)
    {
        return $model->delete();
    }

    public function get(array $params, array $with = [])
    {
        $query = $this->getModel()::query();

        if($params) {
            foreach($params as $param => $value) {
                if(is_array($value)) {
                    $query->whereIn($param, $value);
                } else { 
                    $query->where($param, '=', $value);
                }
            }
        }

        if($with) {
            $query->with($with);
        }

        $result = $query->get();

        return $result->count() ? $result->first() : false;
    }

    public function first(array $data, array $with = [])
    {
        $query = $this->getModel()::query();

        foreach ($data as $key => $value) {
            $query->where($key, $value);
        }

        if (!empty($with)){
            $query->with($with);
        }

        return $query->first();
    }

    public function updateOrCreate(array $identifiableData, array $data)
    {
        if($model = $this->get($identifiableData))
            return $this->update($model, $data);

        return $this->create(array_merge($identifiableData, $data));
    }

    public function getMany(array $params, array $with = [])
    {
        $query = $this->getModel()::query();

        if($params) {
            foreach($params as $param => $value) {
                if(is_array($value)) {
                    $query->whereIn($param, $value);
                } else { 
                    $query->where($param, '=', $value);
                }
            }
        }

        if($with) {
            $query->with($with);
        }

        // $query->orderBy('updated_at', 'desc');

        $result = $query->get();

        return $result->count() ? $result : false;
    }

    public function latest(array $data)
    {
        $query = $this->getModel()::query();

        foreach ($data as $key => $value) {
            $query->where($key, $value);
        }

        return $query->latest()->first();
    }

}
