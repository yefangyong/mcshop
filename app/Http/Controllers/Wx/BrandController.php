<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Services\Goods\BrandServices;
use Illuminate\Http\Request;


class BrandController extends WxController
{
    protected $only = [];

    public function list(Request $request)
    {
        $page  = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $order = $request->input('order', 'desc');
        $sort  = $request->input('sort', 'add_time');
        $list  = BrandServices::getInstance()->getBrandList($page, $limit, $sort, $order,
            ['id', 'name', 'desc', 'pic_url', 'floor_price']);
        return $this->successPaginate($list);
    }

    public function detail(Request $request)
    {
        $id = $request->input('id', 0);
        if (empty($id)) {
            return $this->fail(CodeResponse::PARAM_NOT_EMPTY);
        }
        $brand = BrandServices::getInstance()->getBrand($id);
        if (is_null($brand)) {
            return $this->fail(CodeResponse::PARAM_NOT_EMPTY, '数据不存在');
        }
        return $this->success($brand->toArray());
    }
}
