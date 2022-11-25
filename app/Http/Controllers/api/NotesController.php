<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotesFormRequest;
use App\Repositories\Contracts\NotesRepositoryInterface;
// use App\Models\Notes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{
    protected $user;
    protected $notesRepo;

    public function __construct(NotesRepositoryInterface $notesRepo)
    {
        $this->user = Auth()->guard('api')->user();
        $this->notesRepo = $notesRepo;
    }

    public function index(Request $request)
    {
        $notes = $this->notesRepo->findByUserIdTypeAndUid($this->user->id, $request->type, $request->uid);

        return compact('notes');
    }


    public function store(NotesFormRequest $request)
    {
        if ($request->validated() == false) {
            return response()->json(['error' => 'Erro de validação.'], 200);
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

    public function update(NotesFormRequest $request, $id)
    {
        if ($request->validated() == false) {
            return response()->json(['error' => 'Erro de validação.'], 200);
        }

        $note = $this->notesRepo->findByUserId($this->user->id)->find($id);

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
        // $note = Notes::where('user_id', $this->user->id)->find($id);
        $note = $this->notesRepo->findByUserId($this->user->id)->find($id);
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
