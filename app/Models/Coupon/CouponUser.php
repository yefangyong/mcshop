<?php

namespace App\Models\Coupon;

use App\Models\BaseModel;

/**
 * Class CouponUser
 *
 * @package App\Models\Coupon
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $coupon_id 优惠券ID
 * @property int|null $status 使用状态, 如果是0则未使用；如果是1则已使用；如果是2则已过期；如果是3则已经下架；
 * @property string|null $used_time 使用时间
 * @property string|null $start_time 有效期开始时间
 * @property string|null $end_time 有效期截至时间
 * @property int|null $order_id 订单ID
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereUsedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUser whereUserId($value)
 * @mixin \Eloquent
 */
class CouponUser extends BaseModel
{


}
