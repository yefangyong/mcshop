<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

/**
 * App\Models\Goods\GoodsProduct
 *
 * @property int $id
 * @property int $goods_id 商品表的商品ID
 * @property array $specifications 商品规格值列表，采用JSON数组格式
 * @property float $price 商品货品价格
 * @property int $number 商品货品数量
 * @property string|null $url 商品货品图片
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereSpecifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsProduct whereUrl($value)
 * @mixin \Eloquent
 */
class GoodsProduct extends BaseModel
{
    protected $casts = [
        'specifications' => 'array',
        'price' => 'float'
    ];

}
