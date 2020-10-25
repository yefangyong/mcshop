<?php

namespace App\Models;


class Collect extends BaseModel
{
    //
    protected $table = 'collect';

    protected $casts = [
        'deleted' => 'boolean',
    ];

}
