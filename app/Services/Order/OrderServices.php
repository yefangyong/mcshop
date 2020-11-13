<?php


namespace App\Services\Order;


use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\OrderGoodsSubmit;
use App\Jobs\OverTimeCancelOrder;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Models\Order\Order;
use App\Models\Order\OrderGoods;
use App\Models\Order\OrderStatusTrait;
use App\Models\Promotion\Coupon;
use App\Services\BaseServices;
use App\Services\Goods\GoodsServices;
use App\Services\Promotion\CouponServices;
use App\Services\Promotion\GrouponServices;
use App\Services\SystemServices;
use App\Services\User\AddressServices;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class OrderServices extends BaseServices
{
    use OrderStatusTrait;

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
        $order->address        = $address->province.$address->city.$address->county." ".$address->address_detail;
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

        $orderGoods = $this->getOrderGoodList($orderId);
        /** @var OrderGoods $orderGood */
        foreach ($orderGoods as $orderGood) {
            $row = GoodsServices::getInstance()->addStock($orderGood->product_id, $orderGood->number);
            if ($row == 0) {
                $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
            }
        }
        return true;
    }

    /**
     * @param $orderId
     * @return OrderGoods[]|Builder[]|Collection
     * 获取订单商品的列表
     */
    public function getOrderGoodList($orderId)
    {
        return OrderGoods::query()->whereOrderId($orderId)->get();
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
            $orderSn = $date.Str::random(6);
            if ($this->checkOrderSnValid($orderSn)) {
                Log::warning("订单号获取失败：".$orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号获取失败');
            }
            return $orderSn;
        });
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
