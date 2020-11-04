<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Models\Promotion\Coupon;
use App\Models\Promotion\CouponUser;
use App\Services\Goods\GoodsServices;
use App\Services\Order\CartServices;
use App\Services\Promotion\CouponServices;
use App\Services\SystemServices;
use App\Services\User\AddressServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;


class CartController extends WxController
{
    /**
     * @return JsonResponse
     * @throws BusinessException
     * 购物车下单
     */
    public function checkout()
    {
        $cartId       = $this->verifyInteger('cartId');
        $addressId    = $this->verifyInteger('addressId');
        $couponId     = $this->verifyInteger('couponId');
        $groupRulesId = $this->verifyInteger('grouponRulesId');
        $userCouponId = $this->verifyInteger('userCouponId');
        $userId       = $this->userId();

        //TODO 1、收货地址
        $address   = AddressServices::getInstance()->getAddressOrDefault($userId, $addressId);
        $addressId = $address->id ?? 0;

        //TODO 2、获取商品的列表
        $checkedGoodsList = CartServices::getInstance()->getCheckedGoodsList($userId, $cartId);

        //TODO 3、团购优惠和商品价格
        $grouponPrice      = 0;
        $checkedGoodsPrice = CartServices::getInstance()->getCartPriceCutGroupon($checkedGoodsList, $groupRulesId,
            $grouponPrice);

        //TODO 4、获取当前适合价格的优惠券列表,并且根据优惠折扣降序排序
        $availableCouponLength = 0;
        $couponsUsers          = CouponServices::getInstance()->getUsableCoupons($userId);
        $couponIds             = $couponsUsers->pluck('coupon_id')->toArray();
        $coupons               = CouponServices::getInstance()->getCouponsByIds($couponIds)->keyBy('id');
        $couponsUsers->filter(function (CouponUser $couponUser) use ($coupons, $checkedGoodsPrice) {
            $coupon = $coupons->get($couponUser->coupon_id);
            return CouponServices::getInstance()->checkCouponAndPrice($coupon, $couponUser, $checkedGoodsPrice);
        })->sortByDesc(function (CouponUser $couponUser) use ($coupons) {
            /** @var Coupon $coupon */
            $coupon = $coupons->get($couponUser->coupon_id);
            return $coupon->discount;
        });

        //TODO 5、选择优惠券
        // 这里存在三种情况
        // 1. 用户不想使用优惠券，则不处理
        // 2. 用户想自动使用优惠券，则选择合适优惠券
        // 3. 用户已选择优惠券，则测试优惠券是否合适
        $couponPrice = 0;
        if (is_null($couponId) || $couponId == -1) {
            $userCouponId = -1;
            $couponId     = -1;
        } elseif ($couponId == 0) {
            /** @var CouponUser $couponUser */
            $couponUser   = $couponsUsers->first();
            $couponId     = $couponUser->coupon_id ?? 0;
            $userCouponId = $couponUser->id ?? 0;
            $couponPrice  = CouponServices::getInstance()->getCoupon($couponId)->discount ?? 0;
        } else {
            $coupon     = CouponServices::getInstance()->getCoupon($couponId);
            $couponUser = CouponServices::getInstance()->getCouponUser($userCouponId);
            $isValid    = CouponServices::getInstance()->checkCouponAndPrice($coupon, $couponUser, $checkedGoodsPrice);
            if ($isValid) {
                $couponPrice = $coupon->discount ?? 0;
            }
        }

        //TODO 6、运费
        $freightPrice    = 0;
        $freightPriceMin = SystemServices::getInstance()->getFreightMin();
        if (bccomp($freightPriceMin, $checkedGoodsPrice) == 1) {
            $freightPrice = SystemServices::getInstance()->getFreightValue();
        }

        //TODO 7、计算订单金额
        $orderPrice = bcadd($checkedGoodsPrice, $freightPrice, 1);
        $orderPrice = bcsub($orderPrice, $couponPrice, 1);

        //TODO 8、组装数据，返回
        return $this->success([
            "addressId"             => $addressId,
            "couponId"              => $couponId,
            "userCouponId"          => $userCouponId,
            "cartId"                => $cartId,
            "grouponRulesId"        => $groupRulesId,
            "grouponPrice"          => $grouponPrice,
            "checkedAddress"        => $address,
            "availableCouponLength" => $couponsUsers->count(),
            "goodsTotalPrice"       => $checkedGoodsPrice,
            "freightPrice"          => (int) $freightPrice,
            "couponPrice"           => $couponPrice,
            "orderTotalPrice"       => $orderPrice,
            "actualPrice"           => $orderPrice,
            "checkedGoodsList"      => $checkedGoodsList->toArray(),
        ]);

    }

    /**
     * @return JsonResponse
     * @throws Exception
     * 购物车信息
     */
    public function index()
    {
        $list               = CartServices::getInstance()->getValidCartList($this->userId());
        $goodsCount         = 0;
        $goodsAmount        = 0;
        $checkedGoodsCount  = 0;
        $checkedGoodsAmount = 0;
        foreach ($list as $item) {
            $goodsCount  += $item->number;
            $amount      = bcmul($item->number, $item->price, 2);
            $goodsAmount = bcadd($goodsAmount, $amount, 2);
            if ($item->checked) {
                $checkedGoodsCount  += $item->number;
                $checkedGoodsAmount = bcadd($checkedGoodsAmount, $amount, 2);
            }
        }
        return $this->success([
            'cartList'  => $list->toArray(),
            'cartTotal' => [
                'goodsCount'         => $goodsCount,
                'goodsAmount'        => (double) $goodsAmount,
                'checkedGoodsCount'  => $checkedGoodsCount,
                'checkedGoodsAmount' => (double) $checkedGoodsAmount
            ]
        ]);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 删除购物车
     */
    public function delete()
    {
        $productIds = $this->verifyArrayNotEmpty('productIds');
        $userId     = $this->userId();
        CartServices::getInstance()->delete($userId, $productIds);
        return $this->index();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 更新购物车的数据
     */
    public function update()
    {
        $productId = $this->verifyId('productId');
        $number    = $this->verifyInteger('number');
        $goodsId   = $this->verifyId('goodsId');
        $id        = $this->verifyId('id');

        if ($number <= 0) {
            return $this->badArgumentValue();
        }

        // 1、判断购物车订单是否存在
        $cartData = CartServices::getInstance()->getCartDataById($id);
        if (is_null($cartData)) {
            return $this->badArgumentValue();
        }

        // 2、判断goodsId和productId是否和当前cart里的值一致
        if ($goodsId != $cartData->goods_id || $productId != $cartData->product_id) {
            return $this->badArgumentValue();
        }

        // 3、判断商品是否可以购买
        $goods = GoodsServices::getInstance()->getGoods($goodsId);
        if ($goods->is_on_sale != 1 || is_null($goods)) {
            return $this->fail(CodeResponse::GOODS_UNSHELVE);
        }

        // 4、获得产品规格的信息，判断规则的库存
        $goodsProducts = GoodsServices::getInstance()->getGoodsProductById($productId);
        if ($goodsProducts->number < $number || is_null($goodsProducts)) {
            return $this->fail(CodeResponse::GOODS_NO_STOCK);
        }

        // 5、修改购物车数量
        $cartData->number = $number;
        $row              = $cartData->save();
        if ($row == 0) {
            return $this->fail(CodeResponse::UPDATED_FAIL);
        }
        return $this->success();

    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 修改购物车选中的状态
     */
    public function checked()
    {
        $productIds = $this->verifyArrayNotEmpty('productIds');
        $isChecked  = $this->verifyEnum('isChecked', null, [0, 1]);
        CartServices::getInstance()->updateCartChecked($this->userId(), $productIds, $isChecked === 1);
        return $this->index();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 加入购物车
     */
    public function add()
    {
        $goodsId   = $this->verifyId('goodsId');
        $productId = $this->verifyId('productId');
        $number    = $this->verifyInteger('number', 0);
        $userId    = $this->userId();
        CartServices::getInstance()->add($goodsId, $productId, $number, $userId);
        $count = CartServices::getInstance()->countCartProduct($userId);
        return $this->success($count);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 立即购买
     * 和add方法的区别在于：
     * 1. 如果购物车内已经存在购物车货品，前者的逻辑是数量添加，这里的逻辑是数量覆盖
     * 2. 添加成功以后，前者的逻辑是返回当前购物车商品数量，这里的逻辑是返回对应购物车项的ID
     */
    public function fastAdd()
    {
        $goodsId   = $this->verifyId('goodsId');
        $productId = $this->verifyId('productId');
        $number    = $this->verifyInteger('number', 0);
        $userId    = $this->userId();
        $cart      = CartServices::getInstance()->fastAdd($goodsId, $productId, $number, $userId);
        return $this->success($cart->id);
    }

    /**
     * @return JsonResponse
     * 获取商品的数量
     */
    public function goodsCount()
    {
        $count = CartServices::getInstance()->countCartProduct($this->userId());
        return $this->success(['count' => $count]);
    }


}
