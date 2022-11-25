<?php

namespace App\Repositories\Contracts;

interface VehicleBrandRepositoryInterface
{
    public function findByVehicleTypeId($vehicleTypeId);
}
