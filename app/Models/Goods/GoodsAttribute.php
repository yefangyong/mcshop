<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class GoodsAttribute extends BaseModel
{
    //
    protected $table = 'goods_attribute';

    protected $casts = [
        'deleted' => 'boolean',
    ];

}
