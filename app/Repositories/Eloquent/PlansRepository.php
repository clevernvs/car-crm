<?php

namespace App\Repositories\Eloquent;

use App\Models\Plans;
use App\Repositories\Contracts\PlansRepositoryInterface;

class PlansRepository implements PlansRepositoryInterface
{
    protected $model;

    public function __construct(Plans $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }
}
