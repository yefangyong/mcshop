<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\AddressSaveInput;
use App\Input\PageInput;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Models\Promotion\Coupon;
use App\Models\Promotion\CouponUser;
use App\Models\User\Address;
use App\Services\Goods\GoodsServices;
use App\Services\Order\CartServices;
use App\Services\Promotion\CouponServices;
use App\Services\SystemServices;
use App\Services\User\AddressServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;


class AddressController extends WxController
{
    /**
     * @return JsonResponse
     * 地址列表
     */
    public function list()
    {
        $userId = $this->user()->id;
        $list   = AddressServices::getInstance()->getAddressByUserId($userId);
        return $this->success([
            'total' => $list->count(),
            'page'  => 1,
            'list'  => $list->toArray(),
            'pages' => 1
        ]);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 地址详情
     */
    public function detail()
    {
        $id      = $this->verifyId('id', 0);
        $address = AddressServices::getInstance()->getUserAddress($this->userId(), $id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }
        return $this->success($address);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 更新或者新增地址
     */
    public function save()
    {
        $userId = $this->userId();
        $input  = AddressSaveInput::new();
        AddressServices::getInstance()->saveAddress($userId, $input);
        return $this->success();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 删除地址
     */
    public function delete()
    {
        $id = $this->verifyInteger('id');
        AddressServices::getInstance()->delete($this->userId(), $id);
        return $this->success();
    }
}
