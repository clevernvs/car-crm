<?php

namespace App\Http\Controllers;

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
use App\Models\VehicleModel;
use App\Models\VehicleMotorpower;
use App\Models\VehicleRegdate;
use App\Models\VehicleType;
use App\Models\VehicleVersion;
use Illuminate\Http\Request;

class DataScraping extends Controller
{

    public function index($vehicle_type_id)
    {
        $this->marcas($vehicle_type_id);
        $this->carro();
        $this->moto();
        $this->type();
    }

    public function marcas($vehicle_type_id)
    {
        if ($vehicle_type_id == 2020) {
            $dataCarros = json_decode(file_get_contents(public_path('2020.json')));
            $vehicle_brand = $dataCarros[1];
        }

        if ($vehicle_type_id == 2060) {
            $dataMotos = json_decode(file_get_contents(public_path('2060.json')));
            $vehicle_brand = $dataMotos[0];
        }

        foreach ($vehicle_brand->values_list as $brand) {
            VehicleBrand::firstOrCreate([
                'label' => $brand->label,
                'value' => $brand->value,
                'vehicle_type_id' => $vehicle_type_id,
            ]);

            foreach ($brand->values as $model) {
                VehicleModel::firstOrCreate([
                    'brand_id' => $brand->value,
                    'label' => $model->label,
                    'value' => $model->value,
                    'vehicle_type_id' => $vehicle_type_id,
                ]);

                foreach ($model->values as $version) {
                    VehicleVersion::firstOrCreate([
                        'brand_id' => $brand->value,
                        'model_id' => $model->value,
                        'label' => $version->label,
                        'value' => $version->value,
                    ]);
                }
            }
        }
    }

    public function carro()
    {
        $data = json_decode(file_get_contents(public_path('2020.json')));

        $array = [
            [
                'data' => $data[2],
                'class' => VehicleRegdate::class,
            ],
            [
                'data' => $data[3],
                'class' => VehicleGearbox::class,
            ],
            [
                'data' => $data[4],
                'class' => VehicleFuel::class,
            ],
            [
                'data' => $data[5],
                'class' => VehicleCarSteering::class,
            ],
            [
                'data' => $data[6],
                'class' => VehicleMotorpower::class,
            ],
            [
                'data' => $data[9],
                'class' => VehicleDoors::class,
            ],
            [
                'data' => $data[12],
                'class' => VehicleCarColor::class,
            ],
            [
                'data' => $data[14],
                'class' => VehicleExchange::class,
            ],
            [
                'data' => $data[15],
                'class' => VehicleFinancials::class,
            ],
        ];

        foreach ($array as $item) {
            $item = (object)$item;

            foreach ($item->data->values_list as $value) {
                $valid = $item::class::where('value', $value->value)->first();
                if (empty($valid)) {
                    $item->class::create((array)$value);
                }
            }
        }

        foreach ($data[11]->values_list as $features_car) {
            $valid = VehicleFeatures::where('value', $features_car->value)
                            ->where('vehicle_type_id', 2020)
                            ->first();

            $features_car->vehicle_type_id = 2020;

            if (empty($valid)) {
                VehicleFeatures::create((array)$features_car);
            }
        }


    }

    public function moto()
    {
        $data = json_decode(file_get_contents(public_path('2060.json')));

        foreach ($data[3]->values_list as $value) {
            $valid =  VehicleCubiccms::where('value', $value->value)->first();

            if (empty($value)) {
                VehicleCubiccms::create((array)$value);
            }
        }

        foreach ($data[5]->values_list as $features_moto) {
            $valid = VehicleFeatures::where('value', $features_moto->value)
                            ->where('vehicle_type_id', 2060)
                            ->first();

            $features_moto->vehicle_type_id = 2060;

            if (empty($valid)) {
                VehicleFeatures::create((array)$features_moto);
            }
        }
    }

    public function types()
    {
        $data = [
            [
                'label' => 'Carros, vans e utilitários',
                'value' => 2020,
            ],
            [
                'label' => 'Motos',
                'value' => 2060,
            ],
        ];

        foreach ($data as $item) {
            $valid = VehicleType::where('value', $item['value'])->first();

            if (empty($valid)) {
                VehicleType::create($item);
            }
        }
    }
}
