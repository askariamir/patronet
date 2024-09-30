<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function getAll();
    public function create(array $data);
    public function find($id);
    public function search($brandName, $productCode);
    public function update($id, array $data);
    public function delete($id);
    public function deleteByBrandId($brandId);
    public function insert(array $data);

}
