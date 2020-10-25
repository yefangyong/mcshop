<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class GoodsProduct extends BaseModel
{
    //
    protected $table = 'goods_product';

    protected $casts = [
        'deleted' => 'boolean',
        'specifications' => 'array',
        'price' => 'float'
    ];

}
