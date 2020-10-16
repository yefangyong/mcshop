<?php


namespace App\Http\Controllers\Wx;


use App\CodeResponse;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
    private function codeReturn($codeResponse, $data = null, $info = '')
    {
        list($errno, $errmsg) = $codeResponse;
        $res = ['error' => $errno, 'errmsg' => $info ?: $errmsg];
        if (!is_null($data)) {
            $res['data'] = $data;
        }
        return response()->json($res);
    }

    public function success($data = null, $info = '')
    {
        return $this->codeReturn(CodeResponse::SUCCESS, $data, $info);
    }

    public function fail(array $codedResponse, $info = '')
    {
        return $this->codeReturn($codedResponse, $info);
    }
}
