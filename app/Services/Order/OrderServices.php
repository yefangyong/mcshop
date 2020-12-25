<?php


namespace App\Services\Order;


use App\CodeResponse;
use App\Constant;
use App\Enums\OrderEnums;
use App\Exceptions\BusinessException;
use App\Input\OrderGoodsSubmit;
use App\Input\PageInput;
use App\Jobs\OverTimeCancelOrder;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Models\Order\Order;
use App\Models\Order\OrderGoods;
use App\Models\Order\OrderStatusTrait;
use App\Models\Promotion\Coupon;
use App\Notifications\NewPaidOrderEmailNotify;
use App\Notifications\NewPaidOrderSmsNotify;
use App\Services\BaseServices;
use App\Services\Goods\GoodsServices;
use App\Services\Promotion\CouponServices;
use App\Services\Promotion\GrouponServices;
use App\Services\SystemServices;
use App\Services\User\AddressServices;
use App\Services\User\UserServices;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Throwable;

class OrderServices extends BaseServices
{
    use OrderStatusTrait;

    public function coverOrder(Order $order, $grouponOrders, $goodsList)
    {
        return [
            "id"              => $order->id,
            "orderSn"         => $order->order_sn,
            "actualPrice"     => $order->actual_price,
            "orderStatusText" => Constant::ORDER_STATUS_TEXT_MAP[$order->order_status] ?? '',
            "handleOption"    => $order->getCanHandleOptions(),
            "aftersaleStatus" => $order->aftersale_status,
            "isGroupin"       => in_array($order->id, $grouponOrders),
            "goodsList"       => $goodsList,
        ];
    }

    public function coverOrderGoods(OrderGoods $orderGoods)
    {
        return [
            "id"             => $orderGoods->id,
            "goodsName"      => $orderGoods->goods_name,
            "number"         => $orderGoods->number,
            "picUrl"         => $orderGoods->pic_url,
            "specifications" => $orderGoods->specifications,
            "price"          => $orderGoods->price
        ];
    }

    /**
     * @param $userId
     * @param  PageInput  $page
     * @param $status
     * @param  string[]  $column
     * @return LengthAwarePaginator
     * 获取订单列表信息
     */
    public function getOrderList($userId, PageInput $page, $status, $column = ['*'])
    {
        return Order::query()->where('user_id', $userId)
            ->when(!empty($status), function (Builder $builder) use ($status) {
                return $builder->whereIn('order_status', $status);
            })->orderBy($page->sort, $page->order)->paginate($page->limit, $column, 'page', $page->page);
    }

    /**
     * @param  array  $orderIds
     * @return OrderGoods[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|\think\Collection
     * 根据订单id，获取商品订单列表
     */
    public function getOrderGoodsListsByOrderIds(array $orderIds)
    {
        if (empty($orderIds)) {
            return collect([]);
        }
        return OrderGoods::query()->whereIn('order_id', $orderIds)->get()->groupBy('order_id');
    }

    /**
     * @param $userId
     * @param $orderId
     * @return array
     * @throws BusinessException
     * 订单详情
     */
    public function detail($userId, $orderId)
    {
        $order = $this->getOrderByUserIdAndId($userId, $orderId);
        if (empty($order)) {
            $this->throwBusinessException(CodeResponse::ORDER_UNKNOWN);
        }

        $detail = Arr::only($order->toArray(), [
            "id",
            "orderSn",
            "message",
            "addTime",
            "consignee",
            "mobile",
            "address",
            "goodsPrice",
            "couponPrice",
            "freightPrice",
            'actualPrice',
            "aftersaleStatus"
        ]);

        $detail['orderStatusText'] = Constant::ORDER_STATUS_TEXT_MAP[$order->order_status];
        $detail['handleOption']    = $order->getCanHandleOptions();

        $goodsList         = $this->getOrderGoodList($orderId);
        $detail['expCode'] = $order->ship_channel;
        $detail['expNo']   = $order->ship_sn;
        $detail['expName'] = ExpressServices::getInstance()->getExpressName($order->ship_channel);
        $express           = []; //todo

        return [
            'orderInfo'   => $detail,
            'orderGoods'  => $goodsList,
            'expressInfo' => $express
        ];
    }

    /**
     * @param $userId
     * @param  OrderGoodsSubmit  $input
     * @return Order
     * @throws BusinessException
     */
    public function submit($userId, OrderGoodsSubmit $input)
    {
        //TODO 验证团购活动是否有效
        if (!empty($input->grouponRulesId)) {
            GrouponServices::getInstance()->checkGrouponRulesValid($userId, $input->grouponRulesId);
        }
        //TODO 获取收获地址
        $address = AddressServices::getInstance()->getUserAddress($userId, $input->addressId);
        if (empty($address)) {
            $this->throwBusinessException();
        }
        //TODO 获取购物车的商品列表
        $checkedGoodList = CartServices::getInstance()->getCheckedGoodsList($userId, $input->cartId);
        //TODO 计算商品的总价格（团购优惠金额，货品价格，优惠券优惠价格，运费）
        $grouponPrice      = 0;
        $checkedGoodsPrice = CartServices::getInstance()->getCartPriceCutGroupon($checkedGoodList,
            $input->grouponRulesId, $grouponPrice);
        //TODO 获取优惠券面额
        $couponPrice = 0;
        if ($input->couponId > 0) {
            /** @var Coupon $coupon */
            $coupon     = CouponServices::getInstance()->getCoupon($input->couponId);
            $couponUser = CouponServices::getInstance()->getCouponUser($input->userCouponId);
            $is         = CouponServices::getInstance()->checkCouponAndPrice($coupon, $couponUser, $checkedGoodsPrice);
            if ($is) {
                $couponPrice = $coupon->discount;
            }
        }
        //TODO 运费
        $freightPrice = SystemServices::getInstance()->getFreightPrice($checkedGoodsPrice);
        //TODO 计算订单金额
        $orderTotalPrice = bcadd($checkedGoodsPrice, $freightPrice, 2);
        $orderTotalPrice = bcsub($orderTotalPrice, $couponPrice, 2);
        $orderTotalPrice = max(0, $orderTotalPrice);
        //TODO 保存订单
        $order                 = new Order();
        $order->user_id        = $userId;
        $order->order_sn       = $this->generateOrderSn();
        $order->order_status   = Constant::ORDER_STATUS_CREATE;
        $order->consignee      = $address->name;
        $order->address        = $address->province . $address->city . $address->county . " " . $address->address_detail;
        $order->message        = $input->message ?? " ";
        $order->goods_price    = $checkedGoodsPrice;
        $order->freight_price  = $freightPrice;
        $order->integral_price = 0;
        $order->mobile         = "";
        $order->coupon_price   = $couponPrice;
        $order->order_price    = $orderTotalPrice;
        $order->actual_price   = $orderTotalPrice;
        $order->groupon_price  = $grouponPrice;
        $order->save();
        //TODO 写入订单商品记录（快照）
        $this->saveOrderGoods($checkedGoodList, $order->id);
        //TODO 删除购物车商品记录
        CartServices::getInstance()->clearCartGoods($userId, $input->cartId);
        //TODO 减库存(重点：乐观锁+防止重复请求)
        $this->reduceProductsStock($checkedGoodList);
        //TODO 设置优惠券的状态
        //TODO 添加团购记录
        GrouponServices::getInstance()->saveGrouponData($input->grouponRulesId, $userId, $order->id,
            $input->grouponLinkId);
        //TODO 设置订单支付超时取消订单任务
        dispatch(new OverTimeCancelOrder($userId, $order->id));
        return $order;
    }

    /**
     * @param $userId
     * @param $orderId
     * @param $shipSn
     * @param $shipChannel
     * @return Order|Order[]|Builder|Builder[]|Collection|Model|null
     * @throws BusinessException
     * @throws Throwable
     * 订单发货
     */
    public function ship($userId, $orderId, $shipSn, $shipChannel)
    {
        $order = $this->getOrderByUserIdAndId($userId, $orderId);

        if (empty($order)) {
            $this->throwBusinessException();
        }

        if (!$order->canShipHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能发货');
        }

        $order->order_status = Constant::ORDER_STATUS_SHIP;
        $order->ship_sn      = $shipSn;
        $order->ship_channel = $shipChannel;
        $order->ship_time    = now()->toDateTimeString();

        if ($order->cas() == 0) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }
        //todo 发通知给用户
        return $order;
    }

    /**
     * @param  Order  $order
     * @param $refundType
     * @param $refundContent
     * @return Order
     * @throws BusinessException
     * @throws Throwable
     * 管理员同意退款
     */
    public function agreeRefund(Order $order, $refundType, $refundContent)
    {
        if (!$order->canAgreeRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能同意退款');
        }
        $now                   = now()->toDateTimeString();
        $order->order_status   = Constant::ORDER_STATUS_REFUND_CONFIRM;
        $order->end_time       = $now;
        $order->refund_amount  = $order->actual_price;
        $order->refund_type    = $refundType;
        $order->refund_content = $refundContent;
        $order->refund_time    = $now;

        if ($order->cas() == 0) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }

        //增加库存
        $this->addProductStock($order->id);
        return $order;
    }

    /**
     * @param $orderId
     * @return int
     * 计算订单中商品的数量
     */
    private function countOrderGoods($orderId)
    {
        return OrderGoods::whereOrderId($orderId)->count(['id']);
    }

    /**
     * @param $userId
     * @param $orderId
     * @param  false  $isAuto
     * @return Order|Order[]|Builder|Builder[]|Collection|Model|null
     * @throws BusinessException
     * @throws Throwable
     * 确认收货
     */
    public function confirm($userId, $orderId, $isAuto = false)
    {
        $order = $this->getOrderByUserIdAndId($userId, $orderId);

        if (empty($order)) {
            $this->throwBusinessException();
        }

        if (!$order->canConfirmHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能被确认收货');
        }

        $order->comments     = $this->countOrderGoods($orderId);
        $order->order_status = $isAuto ? Constant::ORDER_STATUS_AUTO_CONFIRM : Constant::ORDER_STATUS_CONFIRM;
        $order->confirm_time = now()->toDateTimeString();

        if ($order->cas() == 0) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }

        return $order;
    }

    /**
     * @param $userId
     * @param $orderId
     * @return bool
     * @throws BusinessException
     */
    public function delete($userId, $orderId)
    {
        $order = $this->getOrderByUserIdAndId($userId, $orderId);

        if (empty($order)) {
            $this->throwBusinessException();
        }

        if (!$order->canDeleteHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能被删除哦');
        }

        $order->delete();

        //todo 处理订单售后的信息
        return true;
    }

    /**
     * @return Order[]|Builder[]|Collection
     * 获取超时未收货的订单
     */
    public function getTimeUnConfirmOrders()
    {
        $days = SystemServices::getInstance()->getUnConfirmOrderTime();
        return Order::query()->where('order_status', Constant::ORDER_STATUS_SHIP)
            ->where('ship_time', '<=', now()->subDays($days))
            ->where('ship_time', '>=', now()->subDays($days + 30))
            ->get();
    }

    /**
     * @throws BusinessException
     * @throws Throwable
     * 自动确认收货
     */
    public function autoConfirm()
    {
        $orders = $this->getTimeUnConfirmOrders();
        foreach ($orders as $order) {
            $this->confirm($order->user_id, $order->id, true);
        }
    }

    /**
     * @param $userId
     * @param $orderId
     * @return Order|Order[]|Builder|Builder[]|Collection|Model|null
     * @throws BusinessException
     * @throws Throwable
     * 用户退款
     */
    public function refund($userId, $orderId)
    {
        $order = $this->getOrderByUserIdAndId($userId, $orderId);

        if (empty($order)) {
            $this->throwBusinessException();
        }

        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款哦');
        }

        $order->order_status = Constant::ORDER_STATUS_REFUND;

        if ($order->cas() == 0) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }
        //todo 发通知给管理员进行退款处理
        return $order;
    }

    /**
     * @param  Order  $order
     * @param $payId
     * @return Order
     * @throws BusinessException
     * @throws Throwable 支付成功，处理订单
     */
    public function payOrder(Order $order, $payId)
    {
        if (!$order->canPayHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_PAY_FAIL, '订单不能被支付');
        }
        $order->pay_id       = $payId;
        $order->pay_time     = now()->toDateTimeString();
        $order->order_status = Constant::ORDER_STATUS_PAY;
        if ($order->cas() == 0) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }

        //处理团购订单
        GrouponServices::getInstance()->payGrouponOrder($order->id);

        //发送邮箱给管理员
        Notification::route('mail', env('MAIL_USERNAME'))->notify(new NewPaidOrderEmailNotify($order->id));

        //发送短信给用户--自行申请短信测试模板
//        $code = random_int(100000, 999999);
//        $user = UserServices::getInstance()->getUserById($order->user_id);
//        $user->mobile = '18656275932';
//        $user->notify(new NewPaidOrderSmsNotify($code, 'SMS_117526525'));
        return $order;
    }

    /**
     * @param  Collection  $checkProductList
     * @throws BusinessException
     * 减去库存，注意并发和重复请求的问题，即幂等性（对于同一个系统，多次重复请求的结果需要是一样的）
     */
    public function reduceProductsStock(Collection $checkProductList)
    {
        $productIds = $checkProductList->pluck('product_id')->toArray();
        $products   = GoodsServices::getInstance()->getGoodsProductsByIds($productIds)->keyBy('id');
        foreach ($checkProductList as $cart) {
            /** @var GoodsProduct $product */
            $product = $products->get($cart->product_id);
            if (empty($product)) {
                $this->throwBusinessException();
            }
            if ($product->number < $cart->number) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
            }
            $row = GoodsServices::getInstance()->reduceStock($product->id, $cart->number);
            if ($row == 0) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
            }
        }
    }

    /**
     * @param $checkedGoodList
     * @param $orderId
     * 保存订单的快照
     */
    public function saveOrderGoods($checkedGoodList, $orderId)
    {
        /** @var Cart $cart */
        foreach ($checkedGoodList as $cart) {
            $orderGoods                 = OrderGoods::new();
            $orderGoods->order_id       = $orderId;
            $orderGoods->goods_id       = $cart->goods_id;
            $orderGoods->goods_sn       = $cart->goods_sn;
            $orderGoods->product_id     = $cart->product_id;
            $orderGoods->goods_name     = $cart->goods_name;
            $orderGoods->pic_url        = $cart->pic_url;
            $orderGoods->price          = $cart->price;
            $orderGoods->number         = $cart->number;
            $orderGoods->specifications = $cart->specifications;
            $orderGoods->save();
        }
    }

    /**
     * @param $userId
     * @param $orderId
     * @return mixed
     * @throws Throwable
     * 用户取消订单
     */
    public function userCancel($userId, $orderId)
    {
        DB::transaction(function () use ($userId, $orderId) {
            $this->cancel($userId, $orderId, 'user');
        });
        return true;
    }

    /**
     * @param $userId
     * @param $orderId
     * @return bool
     * @throws Throwable
     * 管理员取消订单
     */
    public function adminCancel($userId, $orderId)
    {
        DB::transaction(function () use ($userId, $orderId) {
            $this->cancel($userId, $orderId, 'admin');
        });
        return true;
    }

    /**
     * @param $userId
     * @param $orderId
     * @return bool
     * @throws Throwable
     * 系统取消订单
     */
    public function systemCancel($userId, $orderId)
    {
        DB::transaction(function () use ($userId, $orderId) {
            $this->cancel($userId, $orderId, 'system');
        });
        return true;
    }

    /**
     * @param $userId
     * @param $orderId
     * @param  string  $role  支持 user / admin / system
     * @return bool
     * @throws BusinessException
     * 取消订单
     */
    private function cancel($userId, $orderId, $role = 'user')
    {
        $order = $this->getOrderByUserIdAndId($userId, $orderId);

        if (is_null($orderId)) {
            $this->throwBusinessException();
        }

        if (!$order->canCancelHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
        }

        switch ($role) {
            case 'system':
                $order->order_status = Constant::ORDER_STATUS_AUTO_CANCEL;
                break;
            case 'admin':
                $order->order_status = Constant::ORDER_STATUS_ADMIN_CANCEL;
                break;
            default:
                $order->order_status = Constant::ORDER_STATUS_CANCEL;
        }

        if ($order->cas() === 0) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }

        $this->addProductStock($orderId);

        return true;
    }

    /**
     * @param $orderId
     * @throws BusinessException
     * 增加产品的库存
     */
    public function addProductStock($orderId)
    {
        $orderGoods = $this->getOrderGoodList($orderId);
        /** @var OrderGoods $orderGood */
        foreach ($orderGoods as $orderGood) {
            $row = GoodsServices::getInstance()->addStock($orderGood->product_id, $orderGood->number);
            if ($row == 0) {
                $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
            }
        }
    }

    /**
     * @param $orderId
     * @param  string[]  $column
     * @return OrderGoods[]|Builder[]|Collection
     * 获取订单商品的列表
     */
    public function getOrderGoodList($orderId, $column = ['*'])
    {
        return OrderGoods::query()->whereOrderId($orderId)->get($column);
    }


    /**
     * @param $userId
     * @param $orderId
     * @return Order|Order[]|Builder|Builder[]|Collection|Model|null
     * 获取订单的信息
     */
    public function getOrderByUserIdAndId($userId, $orderId)
    {
        return Order::query()->where('user_id', $userId)->find($orderId);
    }

    /**
     * @param $userId
     * @param $orderId
     * @param $status
     * @return bool|int
     * 修改订单的状态
     */
    public function updateOrderStatus($userId, $orderId, $status)
    {
        return Order::query()->where('user_id', $userId)->where('id', $orderId)->update(['order_status' => $status]);
    }

    /**
     * @return mixed
     * @throws BusinessException
     * 获取订单编号
     */
    public function generateOrderSn()
    {
        return retry(5, function () {
            $date    = date('YmdHis');
            $orderSn = $date . Str::random(6);
            if ($this->checkOrderSnValid($orderSn)) {
                Log::warning("订单号获取失败：" . $orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号获取失败');
            }
            return $orderSn;
        });
    }

    /**
     * @param $userId
     * @param $orderId
     * @return array
     * @throws BusinessException
     * 获取微信支付前订单数据
     */
    public function getPayWxOrder($userId, $orderId)
    {
        $order = $this->getPayOrderInfo($userId, $orderId);
        return $order = [
            'out_trade_no' => $order->order_sn,
            'body'         => '订单：' . $order->order_sn,
            'total_fee'    => bcmul($order->actual_price, 100, 2),
        ];
    }

    /**
     * @param $userId
     * @param $orderId
     * @return Order|Order[]|Builder|Builder[]|Collection|Model|null
     * @throws BusinessException
     * 获取订单支付信息
     */
    public function getPayOrderInfo($userId, $orderId)
    {
        $order = $this->getOrderByUserIdAndId($userId, $orderId);
        if (empty($order)) {
            $this->throwBusinessException(CodeResponse::ORDER_UNKNOWN);
        }
        return $order;
    }

    /**
     * @param $userId
     * @param $orderId
     * @return array
     * @throws BusinessException
     * 获取支付宝支付订单信息
     */
    public function getAlipayPayOrder($userId, $orderId)
    {
        $order = $this->getPayOrderInfo($userId, $orderId);
        return [
            'out_trade_no' => $order->order_sn,
            'total_amount' => $order->actual_price,
            'subject'      => 'test subject - 测试'
        ];
    }

    /**
     * @param $data
     * @return Order|Builder|Model|object|null
     * @throws BusinessException
     * @throws Throwable
     * 微信支付回调
     */
    public function wxNotify($data)
    {
        //记录微信支付回调通知的关键数据
        Log::debug('WxNotify data:' . var_export_inline($data));
        $orderSn = $data['out_trade_no'] ?? '';
        $payId   = $data['transaction_id'] ?? '';
        $price   = bcdiv($data['total_price'], 100, 2);
        return $this->notify($price, $orderSn, $payId);
    }

    /**
     * @param $price
     * @param $orderSn
     * @param $payId
     * @return Order|Builder|Model|object|null
     * @throws BusinessException
     * @throws Throwable
     * 支付成功后修改订单状态和数据
     */
    public function notify($price, $orderSn, $payId)
    {
        $order = $this->getOrderByOrderSn($orderSn);
        if (is_null($order)) {
            $this->throwBusinessException(CodeResponse::ORDER_UNKNOWN);
        }
        if ($order->isHadPaid()) {
            return $order;
        }

        if (bccomp($order->actual_price, $price, 2) != 0) {
            Log::error("支付回调，订单{$order->id}金额不一致，请检查，支付回调金额：{$price}，订单金额：{$order->actual_price}");
            $this->throwBusinessException(CodeResponse::FAIL, '订单金额有问题，请检查');
        }
        return $this->payOrder($order, $payId);
    }

    /**
     * @param $data
     * @return Order|Builder|Model|object|null
     * @throws BusinessException
     * @throws Throwable
     * 支付宝支付回调
     */
    public function alipayNotify($data)
    {
        if (!in_array(($data['trade_status'] ?? ''), ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            $this->throwBusinessException();
        }
        $orderSn = $data['out_trade_no'] ?? '';
        $payId   = $data['transaction_id'] ?? '';
        $price   = $data['total_amount'] ?? 0;
        return $this->notify($price, $orderSn, $payId);
    }

    /**
     * @param $orderSn
     * @return Order|Builder|Model|object|null
     * 根据订单编号获取订单数据
     */
    public function getOrderByOrderSn($orderSn)
    {
        return Order::query()->whereOrderSn($orderSn)->first();
    }

    /**
     * @param $orderSn
     * @return bool
     * 检查订单号是否有效
     */
    private function checkOrderSnValid($orderSn)
    {
        return Order::query()->where('order_sn', $orderSn)->exists();
    }


}
