<?php

namespace App\Repositories\Contracts;

interface PlansRepositoryInterface
{
    public function getAll();
    public function findById($id);
}
