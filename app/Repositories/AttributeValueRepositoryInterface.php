<?php

namespace App\Repositories;

interface AttributeValueRepositoryInterface
{
    public function create(array $data);
    public function insert(array $data);

}
