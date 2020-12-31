<?php

namespace App\Models;




/**
 * App\Models\Issue
 *
 * @property int $id
 * @property string|null $question 问题标题
 * @property string|null $answer 问题答案
 * @property \Illuminate\Support\Carbon|null $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Issue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue query()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereUpdateTime($value)
 * @mixin \Eloquent
 */
class Issue extends BaseModel
{
}
