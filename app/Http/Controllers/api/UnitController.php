<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth()->guard('api')->user();
    }

    public function index()
    {
        $units = Units::where('user_id', $this->user->id)->get();

        return compact('units');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Units::$rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $unit = new Units;
        $unit->user_id = $this->user->id;
        $unit->fill($request->all());
        $unit->save();

        if (empty($unit->id)) {
            return $this->error('Erro ao cadastrar unidade.');
        }

        return $unit;
    }


    public function show($id)
    {
        $unit = Units::where('user_id', $this->user->id)->find($id);

        if (empty($unit->id)) {
            return $this->error('Unidade não encontrada.');
        }

        return compact('unit');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), Units::$rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $unit = Units::where('user_id', $this->user->id)->find($id);

        if (empty($unit->id)) {
            return $this->error('Unidade não encontrada.');
        }

        $unit->fill($request->all());

        if (!$unit->save()) {
            return $this->error('Erro ao atualizar dados.');
        }

        return $this->success('Dados atualizados com sucesso!');

    }


    public function destroy($id)
    {
        $unit = Units::where('user_id', $this->user->id)->find($id);

        if (empty($unit->id)) {
            return $this->error('Unidade não encontrada.');
        }

        if (!$unit->save()) {
            return $this->error('Erro ao excluir dados.');
        }

        return $this->success('Unidade excluida com sucesso!');

    }
}
