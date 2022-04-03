<?php

namespace App\ChatApp\General\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CRUDInterface
{
    public function getModel();
    public function find($id);
    public function create(array $data);
    public function update(Model $model, array $data);
    public function delete(Model $model);
    public function latest(array $data);
    
    public function get(array $data, array $with = []);
    public function getMany(array $data, array $with = []); 
    public function first(array $data, array $with = []);
    public function updateOrCreate(array $identifiableData, array $data);

}