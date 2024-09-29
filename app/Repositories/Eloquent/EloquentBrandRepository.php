<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\BrandRepositoryInterface;

class EloquentBrandRepository implements BrandRepositoryInterface
{
    protected $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {

        return $this->model->with(['attributes.values'])->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->with(['products', 'attributes.values'])->find($id);
    }

    public function update($id, array $data)
    {
        $brand = $this->model->find($id);
        if ($brand) {
            $brand->update($data);
            return $brand;
        }
        return null;
    }

    public function delete($id)
    {
        $brand = $this->model->find($id);

        if ($brand) {
            $brand->delete();
            return true;
        }

        return false;
    }



}
