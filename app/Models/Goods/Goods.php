<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

/**
 * App\Models\Goods\Goods
 *
 * @property int $id
 * @property string $goods_sn 商品编号
 * @property string $name 商品名称
 * @property int|null $category_id 商品所属类目ID
 * @property int|null $brand_id
 * @property array|null $gallery 商品宣传图片列表，采用JSON数组格式
 * @property string|null $keywords 商品关键字，采用逗号间隔
 * @property string|null $brief 商品简介
 * @property bool|null $is_on_sale 是否上架
 * @property int|null $sort_order
 * @property string|null $pic_url 商品页面商品图片
 * @property string|null $share_url 商品分享海报
 * @property bool|null $is_new 是否新品首发，如果设置则可以在新品首发页面展示
 * @property bool|null $is_hot 是否人气推荐，如果设置则可以在人气推荐页面展示
 * @property string|null $unit 商品单位，例如件、盒
 * @property string|null $counter_price 专柜价格
 * @property string|null $retail_price 零售价格
 * @property string|null $detail 商品详细介绍，是富文本格式
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereBrief($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCounterPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereGallery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereIsNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereIsOnSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePicUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereRetailPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereShareUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUpdateTime($value)
 * @mixin \Eloquent
 */
class Goods extends BaseModel
{
    protected $casts = [
        'is_hot'     => 'boolean',
        'is_new'     => 'boolean',
        'is_on_sale' => 'boolean',
        'gallery'    => 'array'
    ];

}
