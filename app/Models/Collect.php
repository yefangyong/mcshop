<?php

namespace App\Models;


/**
 * App\Models\Collect
 *
 * @property int $id
 * @property int $user_id 用户表的用户ID
 * @property int $value_id 如果type=0，则是商品ID；如果type=1，则是专题ID
 * @property int $type 收藏类型，如果type=0，则是商品ID；如果type=1，则是专题ID
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Collect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collect query()
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereValueId($value)
 * @mixin \Eloquent
 */
class Collect extends BaseModel
{

}
