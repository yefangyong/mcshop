<?php

namespace App\Models\Order;

use App\Models\BaseModel;

/**
 * App\Models\Order\OrderGoods
 *
 * @property int $id
 * @property int $order_id 订单表的订单ID
 * @property int $goods_id 商品表的商品ID
 * @property string $goods_name 商品名称
 * @property string $goods_sn 商品编号
 * @property int $product_id 商品货品表的货品ID
 * @property int $number 商品货品的购买数量
 * @property string $price 商品货品的售价
 * @property string $specifications 商品货品的规格列表
 * @property string $pic_url 商品货品图片或者商品图片
 * @property int|null $comment 订单商品评论，如果是-1，则超期不能评价；如果是0，则可以评价；如果其他值，则是comment表里面的评论ID。
 * @property \Illuminate\Support\Carbon|null $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods wherePicUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereSpecifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereUpdateTime($value)
 * @mixin \Eloquent
 */
class OrderGoods extends BaseModel
{

}
