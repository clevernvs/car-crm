<?php

namespace App\Repositories\Contracts;

interface VehicleRepositoryInterface
{
    public function findByUserId($userId);
    public function findByUserIdAndVehicleId($userId, $vehicleId);
    public function findWithPhotoByUserId($userId);
    public function findWithPhotoByUserIdAndVehicleId($userId, $vehicleId);
    public function findActiveByUserId($userId);
}
