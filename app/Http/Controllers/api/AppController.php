<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AppController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth()->guard('api')->user();
    }

    public function index()
    {
        $date = Carbon::now();

        $disable             =                      $date->diffInRealHours($this->user->disabled_account, false);
        $delete              = $date->diffInRealHours($this->user->delete_account, false);
        $this->user->expira  = $this->user->next_expiration;
        $this->user->disable = ($disable > 0) ? CarbonInterval::hours($disable)->cascade()->forHumans() : null;
        $this->user->delete  = ($delete > 0) ? CarbonInterval::hours($delete)->cascade()->forHumans() : null;
        $this->user->plan    = $this->user->plan;
        $this->user->vehicle = $this->user->vehicle;
        $this->user->unit    = $this->user->unit;

        $app = $this->user;

        return compact('app');
    }

    public function update(Request $request)
    {
        $rules = [];

        if ($request->domain != $this->user->domain) {
            $rules = array_merge($rules, ['domain' => 'unique:users']);
        }

        if ($request->subdomain != $this->user->subdomain) {
            $rules = array_merge($rules, ['subdomain' => 'unique:users']);
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $this->user->fill($request->all());

        if ($this->user->save() == false) {
            return $this->error('Erro ao atualizar dados.');
        }

        return $this->success('Dados atualizados com sucesso!');
    }
}
