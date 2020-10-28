<?php

namespace App\Models\Coupon;

use App\Models\BaseModel;

class CouponUser extends BaseModel
{
    //
    protected $table = 'coupon_user';

    protected $casts = [
        'deleted'    => 'boolean',
    ];

    public $timestamps = false;

}
