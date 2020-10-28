<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

/**
 * App\Models\Goods\GoodsAttribute
 *
 * @property int $id
 * @property int $goods_id 商品表的商品ID
 * @property string $attribute 商品参数名称
 * @property string $value 商品参数值
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute whereAttribute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsAttribute whereValue($value)
 * @mixin \Eloquent
 */
class GoodsAttribute extends BaseModel
{

}
