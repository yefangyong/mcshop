<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Services\Goods\BrandServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BrandController extends WxController
{
    protected $only = [];

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws BusinessException
     */
    public function list(Request $request)
    {
        $page = PageInput::new();

        $list = BrandServices::getInstance()->getBrandList($page,
            ['id', 'name', 'desc', 'pic_url', 'floor_price']);
        return $this->successPaginate($list);
    }

    public function detail(Request $request)
    {
        $id    = $this->verifyId('id');
        $brand = BrandServices::getInstance()->getBrand($id);
        if (is_null($brand)) {
            return $this->fail(CodeResponse::PARAM_NOT_EMPTY, '数据不存在');
        }
        return $this->success($brand->toArray());
    }
}
