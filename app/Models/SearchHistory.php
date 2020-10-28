<?php


namespace App\Models;


/**
 * App\Models\SearchHistory
 *
 * @property int $id
 * @property int $user_id 用户表的用户ID
 * @property string $keyword 搜索关键字
 * @property string $from 搜索来源，如pc、wx、app
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchHistory whereUserId($value)
 * @mixin \Eloquent
 */
class SearchHistory extends BaseModel
{

}
