<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OwnerFormRequest;
use App\Models\Owner;
use App\Repositories\Contracts\OwnerRepositoryInterface;

class OwnersController extends Controller
{
    protected $user;

    public function __construct(OwnerRepositoryInterface $ownerRepo)
    {
        $this->user      = Auth()->guard('api')->user();
        $this->ownerRepo = $ownerRepo;
    }

    public function index()
    {
        // $owners = Owner::where('user_id', $this->user->id)
        //     ->orderBy('name', 'ASC')
        //     ->paginate(env('APP_PAGINATE_ITEMS'));

        $owners = $this->ownerRepo
                            ->findByUserId()
                            ->paginate(env('APP_PAGINATE_ITEMS'));

        return compact('owners');
    }

    public function store(OwnerFormRequest $request)
    {
        if (! $request->validated()) {
            return response()->json(['error' => 'Erro de validação dos campos.'], 200);
        }

        $owner          = new Owner;
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
        // $owner = Owner::where('user_id', $this->user->id)->find($id);
        $owner = $this->ownerRepo->findByUserId($this->user->id)->find($id);
        if (empty($owner->id)) {
            return $this->error('Nenhum proprietário encontrado.');
        }

        return $owner;
    }

    public function update(OwnerFormRequest $request, $id)
    {
        if (! $request->validated()) {
            return response()->json(['error' => 'Erro de validação dos campos.'], 200);
        }

        $owner = Owner::where('user_id', $this->user->id)->find($id);
        if (empty($owner->id)) {
            return $this->error('Proprietário não encontrado.');
        }

        $owner->fill($request->all());
        if (! $owner->save()) {
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
        $owner = $this->ownerRepo->findByUserId($this->user->id)->find($id);
        if (empty($owner->id)) {
            return $this->error('Proprietário não encontrado.');
        }

        if (! $owner->delete()) {
            return $this->error('Erro ao excluir proprietário.');
        }

        return $this->success('Proprietário excluido com sucesso!');
    }
}
