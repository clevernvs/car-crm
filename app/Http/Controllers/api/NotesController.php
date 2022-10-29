<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Notes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth()->guard('api')->user();
    }

    public function index(Request $request)
    {
        $notes = Notes::where('user_id', $this->user->id)
                        ->where('type', $request->type)
                        ->where('uid', $request->uid)
                        ->with('user')
                        ->orderBy('id', 'DESC')
                        ->paginate(env('APP_PAGINATE_ITEMS'));

        return compact('notes');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Notes::$rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $note = new Notes;
        $note->user_id = $this->user->id;
        $note->fill($request->all());
        $note->save();

        if (empty($note->id)) {
            return $this->error('Erro ao cadastrar nota.');
        }

        return $note->fresh('user');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), Notes::$rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $note = Notes::where('user_id', $this->user->id)->find($id);

        if (empty($note->id)) {
            return $this->error('Nota não encontrada.');
        }

        $note->fill($request->all());

        if ($note->save()) {
            return $this->success('Dados atualizados com sucesso!');
        } else {
            return $this->erro('Erro ao atualizar os dados.');
        }

    }

    public function destroy($id)
    {
        $note = Notes::where('user_id', $this->user->id)->find($id);
        if (empty($note->id)) {
            return $this->erro('Nota não encontrada.');
        }

        if ($note->delete()) {
            return $this->success('Nota apagada com sucesso!');
        } else {
            return $this->erro('Erro ao apagar dados.');
        }
    }
}
