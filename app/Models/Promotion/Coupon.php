<?php

namespace App\Models\Promotion;

use App\Models\BaseModel;

/**
 * App\Models\Coupon\Coupon
 *
 * @property int $id
 * @property string $name 优惠券名称
 * @property string|null $desc 优惠券介绍，通常是显示优惠券使用限制文字
 * @property string|null $tag 优惠券标签，例如新人专用
 * @property int $total 优惠券数量，如果是0，则是无限量
 * @property float|null $discount 优惠金额，
 * @property float|null $min 最少消费金额才能使用优惠券。
 * @property int|null $limit 用户领券限制数量，如果是0，则是不限制；默认是1，限领一张.
 * @property int|null $type 优惠券赠送类型，如果是0则通用券，用户领取；如果是1，则是注册赠券；如果是2，则是优惠券码兑换；
 * @property int|null $status 优惠券状态，如果是0则是正常可用；如果是1则是过期; 如果是2则是下架。
 * @property int|null $goods_type 商品限制类型，如果0则全商品，如果是1则是类目限制，如果是2则是商品限制。
 * @property string|null $goods_value 商品限制值，goods_type如果是0则空集合，如果是1则是类目集合，如果是2则是商品集合。
 * @property string|null $code 优惠券兑换码
 * @property int|null $time_type 有效时间限制，如果是0，则基于领取时间的有效天数days；如果是1，则start_time和end_time是优惠券有效期；
 * @property int|null $days 基于领取时间的有效天数days。
 * @property string|null $start_time 使用券开始时间
 * @property string|null $end_time 使用券截至时间
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereGoodsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereGoodsValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereTimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdateTime($value)
 * @mixin \Eloquent
 */
class Coupon extends BaseModel
{
    protected $casts = [
        'min'      => 'double',
        'discount' => 'double'
    ];

}
