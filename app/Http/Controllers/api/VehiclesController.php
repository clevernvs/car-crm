<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleCarColor;
use App\Models\VehicleCarSteering;
use App\Models\VehicleCubiccms;
use App\Models\VehicleDoors;
use App\Models\VehicleExchange;
use App\Models\VehicleFeatures;
use App\Models\VehicleFinancials;
use App\Models\VehicleFuel;
use App\Models\VehicleGearbox;
use App\Models\VehicleMotorpower;
use App\Models\VehicleRegdate;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VehiclesController extends Controller
{
    protected $user;

    public function __construct() {
       $this->user = Auth()->guard('api')->user();
    }

    public function getData()
    {
        return [
            'vehicle_types' => VehicleType::all(),
            'regdate' => VehicleRegdate::orderBy('label', 'ASC'),
            'gearbox' => VehicleGearbox::all(),
            'fuel' => VehicleFuel::all(),
            'car_steering' => VehicleCarSteering::all(),
            'motorpower' => VehicleMotorpower::all(),
            'doors' => VehicleDoors::all(),
            'features' => VehicleFeatures::all(),
            'car_color' => VehicleCarColor::all(),
            'exchange' => VehicleExchange::all(),
            'financials' => VehicleFinancials::all(),
            'cubccms' => VehicleCubiccms::all(),
        ];
    }

    public function index()
    {
        $vehicles = Vehicle::where('user_id', $this->user->id)
                            ->where('status', 1)
                            ->with('cover', 'vehicleBrand', 'vehicleFuel', 'vehicleColor', 'vehicleGearbox')
                            ->paginate(env('APP_PAGINATE_ITEMS'));

        $vehicles->transform(function ($vehicle) {
            $vehicle->vehicle_model = $vehicle->vehicleModel();
            $vehicle->vehicle_version = $vehicle->vehicleVersion();

            return $vehicle;
        });

        return compact('vehicles');

    }

    public function store()
    {
        $vehicle = Vehicle::with('vehiclePhotos')->firstOrCreate(['user_id' => $this->user->id, 'status' => 0]);
        $vehicle = $vehicle->fresh('vehicle_photos');

        return array_merge(['vehicle' => $vehicle], $this->getData());
    }


    public function show($id)
    {
        $vehicle = Vehicle::where('user_id', $this->user->id)->with('vehicle_photos')->find($id);

        if (empty($vehicle->id)) {
            return $this->error('Veículo não encontrado!');
        }

        $vehicleBrand = $this->brand($vehicle->vehicle_type);
        $vehicleModel = $this->model($vehicle->vehicle_type, $vehicle->vehicle_brand);
        $vehicleVersion = $this->version($vehicle->vehicle_brand, $vehicle->vehicle_model);

        return array_merge(
            ['vehicle' => $vehicle], $vehicleBrand, $vehicleModel, $vehicleVersion, $this->getData());
    }


    public function update(Request $request, $id)
    {
        $request['vehicle_photos'] = $id;
        $validator = Validator::make($request->all(), Vehicle::$rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $vehicle = Vehicle:: where('user_id', $this->user->id)->find($id);

        if (empty($vehicle->id)) {
            return $this->error(' Veículo não encontrado.');
        }

        $vehicle->fill($request->all());
        $vehicle->status = 1;
        $vehicle->uf_url = $this->validateURL($request->uf);
        $vehicle->city_url = $this->validateURL($request->city);

        if ($vehicle->save() == false) {
            return $this->error('Erro ao atualizar os dados!');
        }

        return $this->success('Dados atualizados com sucesso!');

        // if ($vehicle->id) {
        //     $vehicle->fill($request->all());
        //     $vehicle->status = 1;
        //     $vehicle->uf_url = $this->validateURL($request->uf);
        //     $vehicle->city_url = $this->validateURL($request->city);

        //     if ($vehicle->save()) {
        //         return $this->success('Dados atualizados com sucesso!');
        //     }

        //     return $this->error('Erro ao atualizar os dados!');
        // }

        // return $this->error(' Veículo não encontrado.');
    }


    public function destroy($id)
    {
        $vehicle = Vehicle::where('user_id', $this->user->id)->with('vehicle_photos')->find($id);
        if (empty($vehicle->id)) {
            return $this->error(' Veículo não encontrado.');
        }

        // $directory = 'vehicles/' . $this->user->id . '/' . $id;
        $directory = "vehicles/{$this->user->id}/{$id}";

        if ($vehicle->vehicle_photos()->delete()) {
            Storage::deleteDirectory($directory);
        }

        if (!$vehicle->delete()) {
            return $this->error('Erro ao excluir o veículo.');
        }

        return $this->success('Veículo excluído com sucesso!');
    }

    public function brand($vehicleTypeId)
    {
        $vehicleBrand = VehicleBrand::where('vehicle_type_id', $vehicleTypeId)->get();

        return compact('vehicle_brand');
    }

    public function model($vehicleTypeId, $vehicleBrandId)
    {
        # code...
    }

    public function version($vehicleBrandId, $vehicleModelId)
    {
        # code...
    }
}
