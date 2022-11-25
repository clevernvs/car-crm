<?php

namespace App\Repositories\Eloquent;

use App\Models\Owner;
use App\Repositories\Contracts\OwnerRepositoryInterface;

class OwnerRepository implements OwnerRepositoryInterface
{
    protected $model;

    public function __construct(Owner $model)
    {
        $this->model = $model;
    }

    public function findByUserId($userId)
    {
        return $this->model
                        ->where('user_id', $userId)
                        ->orderBy('name', 'ASC');
    }
}
