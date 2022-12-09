<?php

namespace App\Repositories\Contracts;

interface NotesRepositoryInterface
{
    public function findByUserId($userId);

    public function findByUserIdTypeAndUid($userId, $type, $uid);
}
