<?php


namespace App\Services\Order;


use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Services\BaseServices;
use App\Tools\Logs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CartServices extends BaseServices
{

    /**
     * @param $userId
     * @param $productId
     * @param $goodsId
     * @return Cart|Builder|Model|object|null
     * 获取购物车产品
     */
    public function getCartProduct($userId, $productId, $goodsId)
    {
        return Cart::query()->whereUserId($userId)->whereProductId($productId)->whereGoodsId($goodsId)->first();
    }

    /**
     * @param $userId
     * @return int|mixed
     * 计算用户购物车的数量
     */
    public function countCartProduct($userId)
    {
        return Cart::query()->whereUserId($userId)->sum('number');
    }

    /**
     * @param $userId
     * @param  GoodsProduct  $goodsProduct
     * @param  Goods  $goods
     * @param $number
     * @return bool
     * @throws BusinessException
     * add cart
     */
    public function newCart($userId, GoodsProduct $goodsProduct, Goods $goods, $number)
    {
        $cart = Cart::new();
        if ($number > $goodsProduct->number) {
            $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
        }
        $cart->goods_sn       = $goods->goods_sn;
        $cart->goods_name     = $goods->name;
        $cart->pic_url        = $goodsProduct->url ?: $goods->pic_url;
        $cart->price          = $goodsProduct->price;
        $cart->goods_id       = $goods->id;
        $cart->product_id     = $goodsProduct->id;
        $cart->specifications = json_encode($goodsProduct->specifications);
        $cart->checked        = true;
        $cart->user_id        = $userId;
        $cart->number         = $number;
        $cart->save();
        return true;
    }
}
