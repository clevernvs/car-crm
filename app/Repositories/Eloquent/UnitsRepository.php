<?php

namespace App\Repositories\Eloquent;

use App\Models\Units;
use App\Repositories\Contracts\UnitsRepositoryInterface;

class UnitsRepository implements UnitsRepositoryInterface
{
    protected $model;

    public function __construct(Units $model)
    {
        $this->model = $model;
    }

    public function getAllByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findByUserIdAndPlansId($userId, $plansId)
    {
        return $this->model->where('user_id', $userId)->find($plansId);
    }
}
