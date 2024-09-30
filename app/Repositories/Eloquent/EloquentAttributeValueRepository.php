<?php

namespace App\Repositories\Eloquent;

use App\Models\AttributeValue;
use App\Repositories\AttributeValueRepositoryInterface;

class EloquentAttributeValueRepository implements AttributeValueRepositoryInterface
{
    protected $model;

    public function __construct(AttributeValue $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }
}
