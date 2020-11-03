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
     * @throws BusinessException
     * 添加购物车
     */
    public function add()
    {
        $goodsId   = $this->verifyId('goodsId');
        $productId = $this->verifyId('productId');
        $number    = $this->verifyInteger('number', 0);

        if ($number <= 0) {
            return $this->badArgument();
        }

        $goods  = GoodsServices::getInstance()->getGoods($goodsId);
        $userId = $this->userId();

        //判断商品是否不存在或者下架
        if (is_null($goods) || !$goods->is_on_sale) {
            return $this->fail(CodeResponse::GOODS_UNSHELVE);
        }

        $goodProduct = GoodsServices::getInstance()->getGoodsProductById($productId);

        //判断产品是否不存在
        if (is_null($goodProduct)) {
            return $this->badArgumentValue();
        }

        $cartProduct = CartServices::getInstance()->getCartProduct($userId, $productId, $goodsId);
        $cart        = Cart::new();

        //判断购物车中是否已经有数据
        if (is_null($cartProduct)) {
            CartServices::getInstance()->newCart($userId, $goodProduct, $goods, $number);
        } else {
            $num = $number + $cartProduct->number;
            if ($num > $goodProduct->number) {
                return $this->fail(CodeResponse::GOODS_NO_STOCK);
            }
            $cart->number = $num;
            $cart->save();
        }

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
