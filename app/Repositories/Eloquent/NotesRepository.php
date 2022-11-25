<?php

namespace App\Repositories\Eloquent;

use App\Models\Notes;
use App\Repositories\Contracts\NotesRepositoryInterface;

class NotesRepository implements NotesRepositoryInterface
{
    protected $model;

    public function __construct(Notes $model)
    {
        $this->model = $model;
    }

    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId);
    }

    public function findByUserIdTypeAndUid($userId, $type, $uid)
    {
        return $this->model
                        ->where('user_id', $userId)
                        ->where('type', $type)
                        ->where('uid', $uid)
                        ->with('user')
                        ->orderBy('id', 'DESC')
                        ->paginate(env('APP_PAGINATE_ITEMS'));
    }
}
