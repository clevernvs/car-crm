<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $guarded = ['id'];

    public static $rules = [
        'phone'        => 'required"|min:13',
        'city'         => 'required"',
        'uf'           => 'required"',
        'neighborhood' => 'required"',
    ];
}
