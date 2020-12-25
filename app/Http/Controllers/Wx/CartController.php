<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Services\Goods\GoodsServices;
use App\Services\Order\CartServices;
use App\Services\Promotion\CouponServices;
use App\Services\SystemServices;
use App\Services\User\AddressServices;
use Exception;
use Illuminate\Http\JsonResponse;


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

        // 1、收货地址
        $address   = AddressServices::getInstance()->getAddressOrDefault($userId, $addressId);
        $addressId = $address->id ?? 0;

        // 2、获取商品的列表
        $checkedGoodsList = CartServices::getInstance()->getCheckedGoodsList($userId, $cartId);

        // 3、计算团购优惠和商品价格
        $grouponPrice      = 0;
        $checkedGoodsPrice = CartServices::getInstance()->getCartPriceCutGroupon($checkedGoodsList, $groupRulesId,
            $grouponPrice);

        // 4、获取当前合适的优惠券，并返回优惠券的折扣和优惠券的数量
        list($couponId, $userCouponId, $couponPrice, $countUserCoupon) = CouponServices::getInstance()->getUserMeetCoupons($userId,
            $checkedGoodsPrice, $couponId, $userCouponId);

        // 6、运费
        $freightPrice = SystemServices::getInstance()->getFreightPrice($checkedGoodsPrice);

        // 7、计算订单金额
        $orderPrice = bcadd($checkedGoodsPrice, $freightPrice, 1);
        $orderPrice = bcsub($orderPrice, $couponPrice, 1);

        // 8、组装数据，返回
        return $this->success([
            "addressId"             => $addressId,
            "couponId"              => $couponId,
            "userCouponId"          => $userCouponId,
            "cartId"                => $cartId,
            "grouponRulesId"        => $groupRulesId,
            "grouponPrice"          => $grouponPrice,
            "checkedAddress"        => $address,
            "availableCouponLength" => $countUserCoupon,
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
