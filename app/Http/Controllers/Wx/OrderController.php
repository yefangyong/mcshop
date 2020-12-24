<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\OrderGoodsSubmit;
use App\Input\PageInput;
use App\Models\Order\Order;
use App\Models\Promotion\CouponUser;
use App\Services\Order\OrderServices;
use App\Services\Promotion\CouponServices;
use App\Services\Promotion\GrouponServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\InvalidArgumentException;
use Yansongda\Pay\Exceptions\InvalidConfigException;
use Yansongda\Pay\Exceptions\InvalidSignException;


class OrderController extends WxController
{
    protected $except = ['wxNotify', 'alipayNotify'];

    /**
     * @return JsonResponse
     * @throws BusinessException
     */
    public function detail()
    {
        $orderId = $this->verifyId('orderId');
        $detail  = OrderServices::getInstance()->detail($this->userId(), $orderId);
        return $this->success($detail);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 获取订单的列表
     */
    public function list()
    {
        $page   = PageInput::new();
        $status = $this->verifyInteger('showType', 0);
        $orders = OrderServices::getInstance()->getOrderList($this->userId(), $page, $status);
        $orderData    = [];
        /** @var Order $order */
        foreach ($orders as $order) {
            $res['id']              = $order->id;
            $res['orderSn']         = $order->order_sn;
            $res['actualPrice']     = $order->order_price;
            $res['aftersaleStatus'] = $order->aftersale_status;
            $res['orderStatusText'] = Constant::ORDER_STATUS_TEXT_MAP[$order->order_status];
            $res['isGroupin']       = GrouponServices::getInstance()->getGrouponByOrderId($order->id) ? true : false;
            $res['handleOption']    = $order->getCanHandleOptions();
            $res['goodsList']       = OrderServices::getInstance()->getOrderGoodList($order->id, ['number','pic_url','price','id','goods_name','specifications']);
            $orderData[] = $res;
        }
        $data = $this->paginate($orders, $orderData);
        return $this->success($data);

    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * @throws Throwable
     * 提交订单
     */
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

    /**
     * @return RedirectResponse
     * @throws BusinessException
     * H5支付
     */
    public function h5pay()
    {
        $orderId = $this->verifyId('orderId');
        $order   = OrderServices::getInstance()->getPayWxOrder($this->userId(), $orderId);
        return Pay::wechat()->wap($order);
    }

    /**
     * @return Response
     * @throws BusinessException
     * 支付宝支付
     */
    public function h5alipay()
    {
        $orderId = $this->verifyId('orderId');
        $order   = OrderServices::getInstance()->getAlipayPayOrder($this->userId(), $orderId);
        return Pay::alipay()->wap($order);
    }

    /**
     * @return Response
     * @throws InvalidSignException
     * @throws Throwable
     * @throws InvalidConfigException
     * 支付宝支付回调
     */
    public function alipayNotify()
    {
        $data = Pay::alipay()->verify()->toArray();
        Log::info('alipayNotify:' . $data);
        DB::transaction(function ($data) {
            OrderServices::getInstance()->alipayNotify($data);
        });
        return Pay::alipay()->success();
    }

    /**
     * @return Response
     * @throws Throwable
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * 微信回调通知
     */
    public function wxNotify()
    {
        $data = Pay::wechat()->verify();
        $data = $data->toArray();
        DB::transaction(function () use ($data) {
            OrderServices::getInstance()->wxNotify($data);
        });
        return Pay::wechat()->success();
    }
}
