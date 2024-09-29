<?php

namespace App\Repositories\Eloquent;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Repositories\AttributeRepositoryInterface;

class EloquentAttributeRepository implements AttributeRepositoryInterface
{
    protected $model;

    public function __construct(Attribute $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($id, array $data)
    {
        $attribute = $this->model->find($id);
        if ($attribute) {
            $attribute->update($data);
            return $attribute;
        }
        return null;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
    public function deleteByBrandId($brandId)
    {

        $attributes = $this->model->where('brand_id', $brandId)->get();

        foreach ($attributes as $attribute) {

            AttributeValue::where('attribute_id', $attribute->id)->delete();


            $attribute->delete();
        }
    }

}
