<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $casts = [
        'vehicle_features' => Json::class,
        'vehicle_financial' => Json::class,
    ];

    protected $guarded = [
        'id'
    ];

    // static $rules = [
    //     'zipCode' => 'required',
    //     'city' => 'required',
    //     'uf' => 'required',
    //     'vehicle_type' => 'required',
    //     'vehicle_brand' => 'required',
    //     'vehicle_model' => 'required',
    //     'vehicle_version' => 'required',
    //     'vehicle_regdate' => 'required',
    //     'vehicle_fuel' => 'required',
    //     'vehicle_price' => 'required',
    //     'vehicle_photos' => 'exists:vehicle_photos,vehicle_id',
    // ];

    public function cover()
    {
        return $this->hasOne('App\Models\VehiclePhoto', 'vehicle_id', 'id')->orderBy('order', 'ASC');
    }

    public function vehicleBrand()
    {
        return $this->hasOne('App\Models\VehicleBrand', 'value', 'vehicle_brand');
    }

    public function vehicleModel()
    {
        return VehicleModel::where('value', $this->vehicle_model)
                            ->where('brand_id', $this->vehicle_brand)
                            ->first();
    }

    public function vehicleVersion()
    {
        return VehicleVersion::where('value', $this->vehicle_version)
                            ->where('brand_id', $this->vehicle_brand)
                            ->where('model_id', $this->vehicle_model->value)
                            ->first();
    }

    public function vehicleColor()
    {
        return $this->hasOne('App\Models\VehicleCarColor', 'value', 'vehicle_color');
    }

    public function vehicleFuel()
    {
        return $this->hasOne('App\Models\VehicleFuel', 'value', 'vehicle_fuel');
    }

    public function vehicleGearbox()
    {
        return $this->hasOne('App\Models\VehicleGearbox', 'value', 'vehicle_gearbox');
    }

    public function vehiclePhotos()
    {
        return $this->hasMany('App\Models\VehiclePhoto', 'vehicle_id', 'id')->orderBy('order', 'ASC');
    }
}
