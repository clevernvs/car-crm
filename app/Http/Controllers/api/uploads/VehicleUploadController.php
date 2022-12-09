<?php

namespace App\Http\Controllers\api\uploads;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class VehicleUploadController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth()->guard('api')->user;
    }

    public function create(Request $request)
    {
        $file     = $request->file('file');
        $filename = md5(uniqid(time())).strstr($file->getClientOriginalName(), '.');

        $vehicle = Vehicle::where('user_id', $this->user->id)->find($request->id);

        if (! $vehicle) {
            return response()->json(['error' => 'Veículo não encontrado.']);
        }

        if ($request->hasFile('file') && $file->isValid()) {
            $photo = VehiclePhoto::create([
                'user_id'    => $this->user->id,
                'vehicle_id' => $request->id,
                'img'        => $filename,
            ]);

            if ($photo->id) {
                $img = Image::make($request->file)->orientate();
                $img->resize(1000, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $imageName = 'vehicles/'.$this->user->id.'/'.$photo->vehicle_id.'/'.$filename;

                Storage::put($imageName, $img->encode(), 'public');

                return $photo;
            }

            return response()->json(['error' => 'Erro ao cadastrar imagem.']);
        }
    }

    public function update(Request $request)
    {
        foreach ($request->order as $order => $id) {
            $position        = VehiclePhoto::where('user_id', $this->user_id)->find($id);
            $position->order = $order;
            $position->save();
        }

        return response()->json(['success' => 'As posições das imagens foram atualizadas com sucesso!']);
    }

    public function destroy($id)
    {
        $photo = VehiclePhoto::where('user_id', $this->user->id)->find($id);
        if ($photo->id) {
            $path = 'vehicles/'.$this->user->id.'/'.$photo->vehicle_id.'/'.$photo->img;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            if ($photo->delete) {
                return response()->json(['success' => 'Imagem apagada com sucesso!']);
            }

            return response()->json(['error' => 'Erro ao apagar imagem.']);
        }

        return response()->json(['error' => 'Imagem não encontrada']);
    }
}
