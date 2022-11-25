<?php

namespace App\Repositories\Eloquent;

use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;

class VehicleRepository implements VehicleRepositoryInterface
{
    protected $model;

    public function __construct(Vehicle $model)
    {
        $this->model = $model;
    }

    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId);
    }

    public function findByUserIdAndVehicleId($userId, $vehicleId)
    {
        return  $this->model
                        ->where('user_id', $userId)
                        ->find($vehicleId);
    }

    public function findWithPhotoByUserIdAndVehicleId($userId, $vehicleId)
    {
        return $this->model
                        ->where('user_id', $userId)
                        ->with('vehicle_photos')
                        ->find($vehicleId);
    }

    public function findWithPhotoByUserId($userId)
    {
        return $this->model
                        ->where('user_id', $userId)
                        ->with('vehicle_photos');
    }

    public function findActiveByUserId($userId)
    {
        return $this->model
                        ->where('user_id', $$userId)
                        ->where('status', 1)
                        ->with('cover', 'vehicleBrand', 'vehicleFuel', 'vehicleColor', 'vehicleGearbox')
                        ->paginate(env('APP_PAGINATE_ITEMS'));
    }
}
