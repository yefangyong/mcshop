<?php


namespace App\Services\Order;


use App\CodeResponse;
use App\Constant;
use App\Models\Cart\Cart;
use App\Models\Comment;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Services\BaseServices;
use App\Services\User\UserServices;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CartServices extends BaseServices
{

    /**
     * @param $userId
     * @param $productId
     * @param $goodsId
     * @return Cart|Builder|Model|object|null
     * 获取购物车产品
     */
    public function getCartProduct($userId, $productId, $goodsId) {
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
     * @throws \App\Exceptions\BusinessException
     * add cart
     */
    public function newCart($userId, GoodsProduct $goodsProduct, Goods $goods, $number)
    {
        $cart = Cart::new();
        if ($number > $goodsProduct->number) {
            $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
        }
        $cart->goods_sn   = $goods->goods_sn;
        $cart->goods_name = $goods->name;
        $cart->pic_url    = $goodsProduct->url ?: $goods->pic_url;
        $cart->price      = $goodsProduct->price;
        $cart->checked    = true;
        $cart->user_id    = $userId;
        $cart->number     = $number;
        $cart->save();
        return true;
    }
}
