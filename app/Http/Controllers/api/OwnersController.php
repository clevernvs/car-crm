<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OwnersController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth()->guard('api')->user();
    }

    public function index()
    {
        $owners = Owner::where('user_id', $this->user->id)
            ->orderBy('name', 'ASC')
            ->paginate(env('APP_PAGINATE_ITEMS'));

        return compact('owners');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Owner::$rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $owner = new Owner;
        $owner->user_id = $this->user->id;
        $owner->fill($request->all());
        $owner->save();

        if (empty($owner->id)) {
            return $this->error('Erro ao cadastrar proprietário');
        }

        return $owner;
    }


    public function show($id)
    {
        $owner = Owner::where('user_id', $this->user->id)->find($id);
        if (empty($owner->id)) {
            return $this->error('Nenhum proprietário encontrado.');
        }

        return $owner;
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), Owner::$rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $owner = Owner::where('user_id', $this->user->id)->find($id);
        if (empty($owner->id)) {
            return $this->error('Proprietário não encontrado.');
        }

        $owner->fill($request->all());
        if (!$owner->save()) {
            return $this->error('Erro ao atualizar dados.');
        }

        return $this->success('Dados atualizados com sucesso!');

        // if ($owner->save()) {
        //     return $this->success('Dados atualizados com sucesso!');
        // } else {
        //     return $this->error('Erro ao atualizar dados.');
        // }
    }

    public function destroy($id)
    {
        $owner = Owner::where('user_id', $this->user->id)->find($id);
        if (empty($owner->id)) {
            return $this->error('Proprietário não encontrado.');
        }

        if (!$owner->delete()) {
            return $this->error('Erro ao excluir proprietário.');
        }

        return $this->success('Proprietário excluido com sucesso!');
    }
}
