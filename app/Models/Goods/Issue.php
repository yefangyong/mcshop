<?php

namespace App\Models\Goods;

use App\Models\BaseModel;

class Issue extends BaseModel
{
    //
    protected $table = 'issue';

    protected $casts = [
        'deleted' => 'boolean',
    ];

}
