<?php

namespace App\Repositories\Eloquent;

use App\Models\VehicleBrand;
use App\Repositories\Contracts\VehicleBrandRepositoryInterface;

class VehicleBrandRepository implements VehicleBrandRepositoryInterface
{
    protected $model;

    public function __construct(VehicleBrand $model)
    {
        $this->model = $model;
    }

    public function findByVehicleTypeId($vehicleTypeId)
    {
        return $this->model
                        ->where('vehicle_type_id', $vehicleTypeId)
                        ->get();
    }
}
