<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $table = 'owners';
    protected $guarded = ['id'];

    static $rules = [
        'name' => 'required|min:3',
        'phone_1' => 'required|min:9',
    ];

    public function setBirthAttribute($value)
    {
        $this->attibutes['birth'] = Carbon::parse($value);
    }

}
