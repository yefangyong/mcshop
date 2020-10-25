<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class GoodsSpecification extends BaseModel
{
    //
    protected $table = 'goods_specification';

    protected $casts = [
        'deleted' => 'boolean',
    ];

}
