<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\Json;

class Transactions extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $guarded = ['id'];

    protected $casts = ['item' => Json::class];




}
