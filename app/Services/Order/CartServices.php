<?php


namespace App\Services\Order;


use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Models\Cart\Cart;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsProduct;
use App\Services\BaseServices;
use App\Services\Goods\GoodsServices;
use App\Services\Promotion\GrouponServices;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CartServices extends BaseServices
{
    /**
     * @param $userId
     * @param  null  $cartId
     * @return bool|mixed|null
     * @throws Exception
     * 清空购物车
     */
    public function clearCartGoods($userId, $cartId = null)
    {
        if (empty($cartId)) {
            return Cart::query()->where('user_id', $userId)->where('checked', 1)->delete();
        } else {
            return Cart::query()->where('id', $cartId)->where('user_id', $userId)->delete();
        }
    }

    /**
     * @param $checkGoodsLists
     * @param $grouponRulesId
     * @param $grouponPrice
     * @return int|string
     * 获取购物车商品减去团购规则优惠的价格
     */
    public function getCartPriceCutGroupon($checkGoodsLists, $grouponRulesId, &$grouponPrice)
    {
        $grouponRules    = GrouponServices::getInstance()->getGrouponRuleById($grouponRulesId);
        $checkGoodsPrice = 0;
        foreach ($checkGoodsLists as $cart) {
            /** @var Cart $cart */
            if ($grouponRules && $grouponRules->goods_id == $cart->goods_id) {
                $grouponPrice = bcmul($grouponRules->discount, $cart->number, 2);
                $price        = bcsub($cart->price, $grouponRules->discount, 2);
            } else {
                $price = $cart->price;
            }
            $price           = bcmul($price, $cart->number, 2);
            $checkGoodsPrice = bcadd($checkGoodsPrice, $price, 2);
        }
        return $checkGoodsPrice;
    }


    /**
     * @param $userId
     * @param  null  $cartId
     * @return Cart[]|Builder[]|Collection|\Illuminate\Support\Collection|\think\Collection
     * @throws BusinessException
     * 获取用户购物车选中的商品
     */
    public function getCheckedGoodsList($userId, $cartId = null)
    {
        if (empty($cartId) || is_null($cartId)) {
            $checkedGoodsList = $this->getCheckedByUid($userId);
        } else {
            $checkedGoodsList = $this->getCheckedByUidAndCartId($userId, $cartId);
            if (is_null($checkedGoodsList)) {
                $this->throwBusinessException(CodeResponse::SYSTEM_ERROR);
            }
        }
        return $checkedGoodsList;
    }


    /**
     * @param $userId
     * @param $cartId
     * @return Cart|Builder|Model|object|null
     */
    public function getCheckedByUidAndCartId($userId, $cartId)
    {
        return Cart::query()->where('user_id', $userId)->where('id', $cartId)->get();
    }

    /**
     * @param $userId
     * @return Cart[]|Builder[]|Collection
     * 获取用户选择的购物车商品
     */
    public function getCheckedByUid($userId)
    {
        return Cart::query()->whereChecked(1)->whereUserId($userId)->get();
    }


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
     * @return Cart[]|Builder[]|Collection
     * @throws Exception
     * 获取有效购物车的数据
     */
    public function getValidCartList($userId)
    {
        $lists          = $this->getCartList($userId);
        $goodIds        = $lists->pluck('goods_id')->toArray();
        $inValidCartIds = [];
        $goodsList      = GoodsServices::getInstance()->getGoodsListByIds($goodIds)->keyBy('id');
        $lists          = $lists->filter(function (Cart $listItem) use ($goodsList, &$inValidCartIds) {
            /** @var Goods $good */
            $good    = $goodsList->get($listItem->goods_id);
            $isValid = !empty($good) && $good->is_on_sale;
            if (!$isValid) {
                $inValidCartIds[] = $listItem->id;
            }
            return $isValid;
        });
        $this->deleteCartListByIds($inValidCartIds);
        return $lists;
    }

    /**
     * @param $cartIds
     * @return bool|int|mixed|null
     * @throws Exception
     * 批量删除购物车的数据
     */
    public function deleteCartListByIds($cartIds)
    {
        return Cart::query()->whereIn('id', $cartIds)->delete();
    }

    /**
     * @param $userId
     * @param $productIds
     * @return bool|int|mixed|null
     * @throws Exception
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
     * @param $cartProduct
     * @param $goodProduct
     * @param $num
     * @return Cart|null
     * @throws BusinessException 编辑购物车的数量
     */
    public function editCartNum($cartProduct, $goodProduct, $num)
    {
        if ($num > $goodProduct->number) {
            $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
        }
        $cartProduct->number = $num;
        $cartProduct->save();
        return $cartProduct;
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
            return $this->editCartNum($cartProduct, $goodProduct, $num);
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

        if (is_null($cartProduct) || !$cartProduct->exists()) {
            return CartServices::getInstance()->newCart($userId, $goodProduct, $goods, $number);
        } else {
            return $this->editCartNum($cartProduct, $goodProduct, $number);
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
     * @return Cart
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
        $cart->specifications = $goodsProduct->specifications;
        $cart->checked        = true;
        $cart->user_id        = $userId;
        $cart->number         = $number;
        $cart->save();
        return $cart;
    }
}
