<?php

namespace App\Models;


class Comment extends BaseModel
{
    protected $casts = [
        'pic_urls' => 'array'
    ];

}
