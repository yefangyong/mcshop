<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\OrderGoodsSubmit;
use App\Input\PageInput;
use App\Models\Promotion\CouponUser;
use App\Services\Order\OrderServices;
use App\Services\Promotion\CouponServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;


class OrderController extends WxController
{
    public function submit()
    {
        /** @var OrderGoodsSubmit $input */
        $input    = OrderGoodsSubmit::new();
        $lock_key = sprintf("order_submit_%s_%s", $this->userId(), md5(serialize($input)));
        $lock     = Cache::lock($lock_key);

        //加上锁，防止重复请求
        if ($lock->get()) {
            return $this->fail(CodeResponse::FAIL, '请勿重复请求');
        }

        $order = DB::transaction(function () use ($input) {
            return OrderServices::getInstance()->submit($this->userId(), $input);
        });

        return $this->success([
            'orderId'       => $order->id,
            'grouponLikeId' => $input->grouponLinkId
        ]);
    }


    /**
     * @return JsonResponse
     * @throws BusinessException
     * @throws Throwable
     * 用户退款
     */
    public function refund()
    {
        $orderId = $this->verifyId('orderId');
        OrderServices::getInstance()->refund($this->userId(), $orderId);
        return $this->success();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 删除订单
     */
    public function delete()
    {
        $orderId = $this->verifyId('orderId');
        OrderServices::getInstance()->delete($this->userId(), $orderId);
        return $this->success();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * @throws Throwable
     * 用户确认收货
     */
    public function confirm()
    {
        $orderId = $this->verifyId('orderId');
        OrderServices::getInstance()->confirm($this->userId(), $orderId);
        return $this->success();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * @throws Throwable
     * 用户主动取消订单
     */
    public function cancel()
    {
        $orderId = $this->verifyId('orderId');
        OrderServices::getInstance()->userCancel($this->userId(), $orderId);
        return $this->success();
    }
}
