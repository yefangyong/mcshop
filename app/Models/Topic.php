<?php

namespace App\Models;




/**
 * App\Models\Topic
 *
 * @property int $id
 * @property string $title 专题标题
 * @property string|null $subtitle 专题子标题
 * @property string|null $content 专题内容，富文本格式
 * @property string|null $price 专题相关商品最低价
 * @property string|null $read_count 专题阅读量
 * @property string|null $pic_url 专题图片
 * @property int|null $sort_order 排序
 * @property string|null $goods 专题相关商品，采用JSON数组格式
 * @property \Illuminate\Support\Carbon|null $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic query()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereGoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic wherePicUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereReadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereUpdateTime($value)
 * @mixin \Eloquent
 */
class Topic extends BaseModel
{
    public $hidden = ['content', 'sort_order', 'goods', 'add_time', 'update_time', 'deleted'];
}
