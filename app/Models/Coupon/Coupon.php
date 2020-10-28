<?php

namespace App\Models\Coupon;

use App\Models\BaseModel;

class Coupon extends BaseModel
{
    protected $casts = [
        'min'      => 'double',
        'discount' => 'double'
    ];

}
