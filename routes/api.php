<?php

use App\Http\Controllers\api\AppController;
// use App\Http\Controllers\api\LogoController;
use App\Http\Controllers\api\NotesController;
use App\Http\Controllers\api\OwnersController;
use App\Http\Controllers\api\PayController;
use App\Http\Controllers\api\UnitController;
use App\Http\Controllers\api\uploads\VehicleUploadController;
use App\Http\Controllers\api\VehiclesController;
// use App\Http\Controllers\webservice\WebserviceController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'vehicles' => VehiclesController::class,
    'notes' => NotesController::class,
    'owners' => OwnersController::class,
    'units' => UnitController::class,
]);

Route::resource('/app', AppController::class);

Route::prefix('pay')->group(function () {
    Route::get('plans', [PayController::class, 'plans']);
});

Route::prefix('upload')->group(function () {
    Route::resource('logo', LogoController::class)->only(['store', 'destroy']);
    Route::resource('vehicle', VehicleUploadController::class)->only(['create', 'update', 'destroy']);
});

Route::get('vehicles/{vehicle_type}/brand', [VehiclesController::class, 'brand']);
Route::get('vehicles/{vehicle_type}/{vehicle_brand}/model', [VehiclesController::class, 'model']);
Route::get('vehicles/{vehicle_brand}/{vehicle_model}/version', [VehiclesController::class, 'version']);

Route::prefix('webservice')->group(function () {
    Route::post('cep', [WebserviceController::class, 'cep']);
});

