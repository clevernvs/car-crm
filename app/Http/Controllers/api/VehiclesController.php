<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleFormRequest;
use App\Models\Vehicle;
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
use App\Repositories\Contracts\VehicleBrandRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class VehiclesController extends Controller
{
    protected $user;

    protected $vehicleRepo;

    protected $vehicleBrandRepo;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepo,
        VehicleBrandRepositoryInterface $vehicleBrandRepo
        // VehicleTypeRepositoryInterface $vehicleTypeRepo,
        // VehicleRegdateRepositoryInterface $vehicleRegdateRepo,
        // VehicleGearboxRepositoryInterface $vehicleGearboxRepo,
        // VehicleFuelRepositoryInterface $vehicleFuelRepo,
        // VehicleCarSteeringRepositoryInterface $vehicleCarSteeringRepo,
        // VehicleMotorpowerRepositoryInterface $vehicleMotorpowerRepo,
        // VehicleDoorsRepositoryInterface $vehicleDoorsRepo,
        // VehicleFeaturesRepositoryInterface $vehicleFeaturesRepo,
        // VehicleCarColorRepositoryInterface $vehicleCarColorRepo,
        // VehicleExchangeRepositoryInterface $vehicleExchangeRepo,
        // VehicleFinancialsRepositoryInterface $vehicleFinancialsRepo,
        // VehicleCubiccmsRepositoryInterface $vehicleCubiccmsRepo,
    ) {
        $this->user             = Auth()->guard('api')->user();
        $this->vehicleRepo      = $vehicleRepo;
        $this->vehicleBrandRepo = $vehicleBrandRepo;
    //    $this->vehicleTypeRepo = $vehicleTypeRepo;
    //    $this->vehicleRegdateRepo = $vehicleRegdateRepo;
    //    $this->vehicleGearboxRepo = $vehicleGearboxRepo;
    //    $this->vehicleFuelRepo = $vehicleFuelRepo;
    //    $this->vehicleCarSteeringRepo = $vehicleCarSteeringRepo;
    //    $this->vehicleMotorpowerRepo = $vehicleMotorpowerRepo;
    //    $this->vehicleDoorsRepo = $vehicleDoorsRepo;
    //    $this->vehicleFeaturesRepo = $vehicleFeaturesRepo;
    //    $this->vehicleCarColorRepo = $vehicleCarColorRepo;
    //    $this->vehicleExchangeRepo = $vehicleExchangeRepo;
    //    $this->vehicleFinancialsRepo = $vehicleFinancialsRepo;
    //    $this->vehicleCubiccmsRepo = $vehicleCubiccmsRepo;
    }

    public function getData()
    {
        return [
            'vehicle_types' => VehicleType::all(),
            'regdate'       => VehicleRegdate::orderBy('label', 'ASC'),
            'gearbox'       => VehicleGearbox::all(),
            'fuel'          => VehicleFuel::all(),
            'car_steering'  => VehicleCarSteering::all(),
            'motorpower'    => VehicleMotorpower::all(),
            'doors'         => VehicleDoors::all(),
            'features'      => VehicleFeatures::all(),
            'car_color'     => VehicleCarColor::all(),
            'exchange'      => VehicleExchange::all(),
            'financials'    => VehicleFinancials::all(),
            'cubccms'       => VehicleCubiccms::all(),
        ];
    }

    public function index()
    {
        $vehicles = $this->vehicleRepo->findActiveByUserId($this->user->id);

        $vehicles->transform(function($vehicle) {
            $vehicle->vehicle_model   = $vehicle->vehicleModel();
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
        $vehicle = $this->vehicleRepo->findWithPhotoByUserIdAndVehicleId($this->user->id, $id);
        if (empty($vehicle->id)) {
            return $this->error('Veículo não encontrado!');
        }

        $vehicleBrand   = $this->brand($vehicle->vehicle_type);
        $vehicleModel   = $this->model($vehicle->vehicle_type, $vehicle->vehicle_brand);
        $vehicleVersion = $this->version($vehicle->vehicle_brand, $vehicle->vehicle_model);

        return array_merge(['vehicle' => $vehicle], $vehicleBrand, $vehicleModel, $vehicleVersion, $this->getData());
    }

    public function update(VehicleFormRequest $request, $id)
    {
        $request['vehicle_photos'] = $id;

        if ($request->validated() == false) {
            return response()->json(['error' => 'Erro de validação.'], 200);
        }

        $vehicle = $this->vehicleRepo->findByUserIdAndVehicleId($this->user->id, $id);

        if (empty($vehicle->id)) {
            return $this->error(' Veículo não encontrado.');
        }

        $vehicle->fill($request->all());
        $vehicle->status   = 1;
        $vehicle->uf_url   = $this->validateURL($request->uf);
        $vehicle->city_url = $this->validateURL($request->city);

        if ($vehicle->save() == false) {
            return $this->error('Erro ao atualizar os dados!');
        }

        return $this->success('Dados atualizados com sucesso!');
    }

    public function destroy($id)
    {
        $vehicle = $this->vehicle->findWithPhotoByUserIdAndVehicleId($this->user->id, $id);
        if (empty($vehicle->id)) {
            return $this->error(' Veículo não encontrado.');
        }

        $directory = "vehicles/{$this->user->id}/{$id}";

        if ($vehicle->vehicle_photos()->delete()) {
            Storage::deleteDirectory($directory);
        }

        if (! $vehicle->delete()) {
            return $this->error('Erro ao excluir o veículo.');
        }

        return $this->success('Veículo excluído com sucesso!');
    }

    public function brand($vehicleTypeId)
    {
        $vehicleBrand = $this->vehicleBrandRepo->findByVehicleTypeId($vehicleTypeId);

        return compact('vehicle_brand');
    }

    public function model($vehicleTypeId, $vehicleBrandId)
    {
        // code...
    }

    public function version($vehicleBrandId, $vehicleModelId)
    {
        // code...
    }
}
