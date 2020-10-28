<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

/**
 * App\Models\Goods\Category
 *
 * @property int $id
 * @property string $name 类目名称
 * @property string $keywords 类目关键字，以JSON数组格式
 * @property string|null $desc 类目广告语介绍
 * @property int $pid 父类目ID
 * @property string|null $icon_url 类目图标
 * @property string|null $pic_url 类目图片
 * @property string|null $level
 * @property int|null $sort_order 排序
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePicUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdateTime($value)
 * @mixin \Eloquent
 */
class Category extends BaseModel
{

}
