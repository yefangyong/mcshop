<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class GoodsProduct extends BaseModel
{
    protected $casts = [
        'specifications' => 'array',
        'price' => 'float'
    ];

}
