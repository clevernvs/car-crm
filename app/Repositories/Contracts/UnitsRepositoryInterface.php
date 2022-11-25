<?php

namespace App\Repositories\Contracts;

interface UnitsRepositoryInterface
{
    public function getAllByUserId($userId);
    public function findByUserIdAndPlansId($userId, $plansId);
}
