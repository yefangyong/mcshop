<?php

namespace App\Models\Coupon;

use App\Models\BaseModel;

class Coupon extends BaseModel
{
    //
    protected $table = 'coupon';

    protected $casts = [
        'deleted'  => 'boolean',
        'min'      => 'double',
        'discount' => 'double'
    ];

}
