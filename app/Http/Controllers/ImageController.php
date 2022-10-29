<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ImageController extends Controller
{
    public function thumb(Request $request, $path = null, $img = null)
    {
        $user = ($request->user) ? (int)$request->user . '/' : '';
        $subPatch = ($request->subPatch) ? $request->subPatch . '/' : '';
        $width = ($request->width) ? (int)$request->width : null;
        $height = ($request->height) ? (int)$request->height : null;

        $path = $path . '/' . $user . $subPatch . $img;
        $url = Storage::get($path);

        if (!$width && !$height) {
            $imagem = Image::cache(function ($image) use ($url) {
                $image->make($url);
            });
        } else {
            $imagem = Image::cache(function ($image) use ($url, $width, $height) {
                if ($width && $height) {
                    $image->make($url)->fit($width, $height);
                } else {
                    $image->make($url)
                        ->resize($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                }
            });
        }

        if (isset($imagem)) {
            return Response::make($imagem, 200, ['Content-type' => 'image'])->setMaxAge(864000)->setPublic();
        }
    }
}
