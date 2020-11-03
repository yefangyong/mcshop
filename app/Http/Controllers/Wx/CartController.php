<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Models\Promotion\CouponUser;
use App\Services\Goods\GoodsServices;
use App\Services\Order\CartServices;
use App\Services\Promotion\CouponServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;


class CartController extends WxController
{

    /**
     * @return JsonResponse
     * 删除购物车
     */
    public function delete()
    {
        $productIds = $this->verifyArrayNotEmpty('productIds');
        $userId     = $this->userId();
        CartServices::getInstance()->delete($userId, $productIds);
        $list = CartServices::getInstance()->getCartList($userId);
        return $this->success($list);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 更新购物车
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
     *
     */
    public function checked()
    {
        $productIds = $this->verifyArrayNotEmpty('productIds');
        $isChecked = $this->verifyEnum('isChecked',null, [0, 1]);
        CartServices::getInstance()->updateCartChecked($this->userId(), $productIds, $isChecked === 1);
        $list = CartServices::getInstance()->getCartList($this->userId());
        return $this->success($list);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 添加购物车
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
     * 获取商品的数量
     */
    public function goodsCount()
    {
        $count = CartServices::getInstance()->countCartProduct($this->userId());
        return $this->success(['count' => $count]);
    }


}
