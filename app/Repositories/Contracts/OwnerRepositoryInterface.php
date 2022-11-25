<?php

namespace App\Repositories\Contracts;

interface OwnerRepositoryInterface
{
    public function findByUserId($userId);
}
