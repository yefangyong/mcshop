<?php

namespace App\Models\Promotion;

use App\Models\BaseModel;

/**
 * App\Models\Promotion\GrouponRules
 *
 * @property int $id
 * @property int $goods_id 商品表的商品ID
 * @property string $goods_name 商品名称
 * @property string|null $pic_url 商品图片或者商品货品图片
 * @property string $discount 优惠金额
 * @property int $discount_member 达到优惠条件的人数
 * @property string|null $expire_time 团购过期时间
 * @property int|null $status 团购规则状态，正常上线则0，到期自动下线则1，管理手动下线则2
 * @property \Illuminate\Support\Carbon $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules query()
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereDiscountMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereExpireTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules wherePicUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrouponRules whereUpdateTime($value)
 * @mixin \Eloquent
 */
class GrouponRules extends BaseModel
{


}
