<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class Goods extends BaseModel
{
    protected $casts = [
        'is_hot'     => 'boolean',
        'is_new'     => 'boolean',
        'is_on_sale' => 'boolean',
        'gallery'    => 'array'
    ];

}
