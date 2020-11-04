<?php

namespace App\Models;

/**
 * App\Models\System
 *
 * @property int $id
 * @property string $key_name 系统配置名
 * @property string $key_value 系统配置值
 * @property \Illuminate\Support\Carbon|null $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|System newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|System newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|System query()
 * @method static \Illuminate\Database\Eloquent\Builder|System whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereKeyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereKeyValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereUpdateTime($value)
 * @mixin \Eloquent
 */
class System extends BaseModel
{

}
