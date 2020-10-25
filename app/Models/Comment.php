<?php

namespace App\Models;


class Comment extends BaseModel
{
    //
    protected $table = 'comment';

    protected $casts = [
        'deleted' => 'boolean',
        'pic_urls' => 'array'
    ];

}
