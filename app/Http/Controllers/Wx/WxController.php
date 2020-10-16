<?php


namespace App\Http\Controllers\Wx;


use App\CodeResponse;
use App\Http\Controllers\Controller;

class WxController extends Controller
{

    protected $only;
    protected $except;

    public function __construct()
    {
        $option = [];
        if (!is_null($this->only)) {
            $option['only'] = $this->only;
        }

        if (!is_null($this->except)) {
            $option['except'] = $this->except;
        }

        $this->middleware('auth:wx', $option);
    }

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
