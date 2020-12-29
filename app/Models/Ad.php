<?php

namespace App\Models;



/**
 * App\Models\Ad
 *
 * @property int $id
 * @property string $name 广告标题
 * @property string $link 所广告的商品页面或者活动页面链接地址
 * @property string $url 广告宣传图片
 * @property int|null $position 广告位置：1则是首页
 * @property string|null $content 活动内容
 * @property string|null $start_time 广告开始时间
 * @property string|null $end_time 广告结束时间
 * @property int|null $enabled 是否启动
 * @property \Illuminate\Support\Carbon|null $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Ad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ad query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ad whereUrl($value)
 * @mixin \Eloquent
 */
class Ad extends BaseModel
{

}
