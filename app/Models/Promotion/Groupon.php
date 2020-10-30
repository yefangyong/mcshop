<?php

namespace App\Models\Promotion;

use App\Models\BaseModel;

/**
 * App\Models\Promotion\Groupon
 *
 * @property int $id
 * @property int $order_id 关联的订单ID
 * @property int|null $groupon_id 如果是开团用户，则groupon_id是0；如果是参团用户，则groupon_id是团购活动ID
 * @property int $rules_id 团购规则ID，关联litemall_groupon_rules表ID字段
 * @property int $user_id 用户ID
 * @property string|null $share_url 团购分享图片地址
 * @property int $creator_user_id 开团用户ID
 * @property string|null $creator_user_time 开团时间
 * @property int|null $status 团购活动状态，开团未支付则0，开团中则1，开团失败则2
 * @property \Illuminate\Support\Carbon $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereCreatorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereCreatorUserTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereGrouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereRulesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereShareUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groupon whereUserId($value)
 * @mixin \Eloquent
 */
class Groupon extends BaseModel
{

}
