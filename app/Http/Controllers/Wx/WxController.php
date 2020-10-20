<?php


namespace App\Http\Controllers\Wx;


use App\CodeResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    protected function paginate($page)
    {
        if ($page instanceof LengthAwarePaginator) {
            return [
                'total' => $page->total(),
                'page'  => $page->currentPage(),
                'limit' => $page->perPage(),
                'pages' => $page->lastPage(),
                'list'  => $page->items()
            ];
        }

        if ($page instanceof Collection) {
            $page = $page->toArray();
        }

        if (!is_array($page)) {
            return $page;
        }

        $total = count($page);
        return [
            'total' => $total,
            'page'  => 1,
            'limit' => $total,
            'pages' => 1,
            'list'  => $page
        ];
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
        } else {
            $res['data'] = $data;
        }
        return response()->json($res);
    }

    public function successPaginate($page)
    {
        return $this->success($this->paginate($page));
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
