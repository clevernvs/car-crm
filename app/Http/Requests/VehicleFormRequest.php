<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'zipCode' => 'required',
            'city' => 'required',
            'uf' => 'required',
            'vehicle_type' => 'required',
            'vehicle_brand' => 'required',
            'vehicle_model' => 'required',
            'vehicle_version' => 'required',
            'vehicle_regdate' => 'required',
            'vehicle_fuel' => 'required',
            'vehicle_price' => 'required',
            'vehicle_photos' => 'exists:vehicle_photos,vehicle_id',
        ];
    }
}
