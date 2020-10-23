<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class Goods extends BaseModel
{
    //
    protected $table = 'goods';

    protected $casts = [
        'deleted' => 'boolean',
        'is_hot'  => 'boolean',
        'is_new'  => 'boolean'
    ];

}
