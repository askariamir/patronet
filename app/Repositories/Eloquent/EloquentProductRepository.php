<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;

class EloquentProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['brand', 'attributeValues.attribute'])->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->with(['brand', 'attributeValues.attribute'])->find($id);
    }


    public function update($id, array $data)
    {
        $product = $this->model->find($id);
        if ($product) {
            $product->update($data);
            return $product;
        }
        return null;
    }

    public function delete($id)
    {
        $product = $this->model->find($id);

        if ($product) {
            $product->delete();
            return true;
        }

        return false;
    }


    public function search($brandName, $productCode)
    {
        return $this->model->whereHas('brand', function ($query) use ($brandName) {
            if ($brandName) {
                $query->where('name', 'like', "%{$brandName}%");
            }
        })
            ->when($productCode, function ($query, $productCode) {
                $query->where('product_code', 'like', "%{$productCode}%");
            })
            ->with('brand')
            ->get();
    }

    public function deleteByBrandId($brandId)
    {
        return $this->model->where('brand_id', $brandId)->delete();
    }
}
