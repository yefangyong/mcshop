<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class Brand extends BaseModel
{
    //
    protected $table = 'brand';

    protected $casts = [
        'deleted' => 'boolean'
    ];

}
