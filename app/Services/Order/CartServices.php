<?php


namespace App\Services\Order;


use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Services\BaseServices;
use App\Services\Goods\GoodsServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CartServices extends BaseServices
{
    /**
     * @param $userId
     * @param $productIds
     * @param $isChecked
     * @return bool|int
     * 修改购物车选中的状态
     */
    public function updateCartChecked($userId, $productIds, $isChecked)
    {
        return Cart::query()->whereUserId($userId)->whereIn('product_id',
            $productIds)->update(['checked' => $isChecked]);
    }

    /**
     * @param $userId
     * @return Cart[]|Builder[]|Collection
     * 获取用户购物车的数据
     */
    public function getCartList($userId)
    {
        return Cart::query()->whereUserId($userId)->get();
    }

    /**
     * @param $userId
     * @param $productIds
     * @return bool|int|mixed|null
     * @throws \Exception
     * 删除购物车商品
     */
    public function delete($userId, $productIds)
    {
        return Cart::query()->where('user_id', $userId)->whereIn('product_id', $productIds)->delete();
    }

    /**
     * @param $cartId
     * @param  string[]  $column
     * @return Cart|Cart[]|Builder|Builder[]|Collection|Model|null
     * 获取购物车的数据
     */
    public function getCartDataById($cartId, $column = ['*'])
    {
        return Cart::query()->find($cartId, $column);
    }

    /**
     * @param $goodsId
     * @param $productId
     * @return array
     * @throws BusinessException
     * 获取商品的信息
     */
    public function getGoodInfo($goodsId, $productId)
    {
        $goods = GoodsServices::getInstance()->getGoods($goodsId);

        //1、判断商品是否存在或者下架
        if (is_null($goods) || !$goods->is_on_sale) {
            $this->throwBusinessException(CodeResponse::GOODS_UNSHELVE);
        }

        $goodProduct = GoodsServices::getInstance()->getGoodsProductById($productId);

        //2、判断产品是否存在
        if (is_null($goodProduct)) {
            $this->throwBusinessException();
        }
        return [$goods, $goodProduct];
    }

    /**
     * @param $goodProduct
     * @param $num
     * @return Cart|null
     * @throws BusinessException
     * 编辑购物车的数量
     */
    public function editCartNum($goodProduct, $num)
    {
        $cart = Cart::new();
        if ($num > $goodProduct->number) {
            $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
        }
        $cart->number = $num;
        $cart->save();
        return $cart;
    }

    /**
     * @param $goodsId
     * @param $productId
     * @param $number
     * @param $userId
     * @throws BusinessException
     * 添加购物车
     */
    public function add($goodsId, $productId, $number, $userId)
    {

        list($goods, $goodProduct) = $this->getGoodInfo($goodsId, $productId);
        $cartProduct = CartServices::getInstance()->getCartProduct($userId, $productId, $goodsId);

        if (is_null($cartProduct)) {
            return CartServices::getInstance()->newCart($userId, $goodProduct, $goods, $number);
        } else {
            $num = $number + $cartProduct->number;
           return  $this->editCartNum($goodProduct, $num);
        }
    }

    /**
     * @param $goodsId
     * @param $productId
     * @param $number
     * @param $userId
     * @return Cart|bool|null
     * @throws BusinessException
     * 立即购买
     */
    public function fastAdd($goodsId, $productId, $number, $userId)
    {
        list($goods, $goodProduct) = $this->getGoodInfo($goodsId, $productId);
        $cartProduct = CartServices::getInstance()->getCartProduct($userId, $productId, $goodsId);

        if (is_null($cartProduct)) {
            return CartServices::getInstance()->newCart($userId, $goodProduct, $goods, $number);
        } else {
           return  $this->editCartNum($goodProduct, $number);
        }
    }

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
