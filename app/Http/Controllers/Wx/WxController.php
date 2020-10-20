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
        $res = ['errno' => $errno, 'errmsg' => $info ?: $errmsg];
        if (is_array($data)) {
            $data        = array_filter($data, function ($item) {
                return $item !== null;
            });
            $res['data'] = $data;
        }
        return response()->json($res);
    }

    public function success($data = null, $info = '')
    {
        return $this->codeReturn(CodeResponse::SUCCESS, $data, $info);
    }

    public function fail(array $codedResponse, $info = '', $data = null)
    {
        return $this->codeReturn($codedResponse, $data, $info);
    }
}
