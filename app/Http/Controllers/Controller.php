<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($message = 'Arquivo excluido com sucesso.', $time = 1200)
    {
        return response()->json([
            'status'  => 200,
            'success' => $message,
            'time'    => $time,
        ], 200);
    }

    public function error($message = 'Erro ao excluir o arquivo', $time = 1200)
    {
        return response()->json([
            'status' => 400,
            'error'  => $message,
            'time'   => $time,
        ], 200);
    }

    public function validateURL($string)
    {
        $Format      = [];
        $Format['a'] = '';
        $Format['b'] = '';

        $Data = strtr(utf8_decode($string), utf8_decode($Format['a']), $Format['b']);
        $Data = strip_tags(trim($Data));
        $Data = str_replace('', '-', $Data);
        $Data = str_replace(['----', '----', '----', '----'], '-', $Data);

        return strtolower(utf8_encode($Data));
    }
}
