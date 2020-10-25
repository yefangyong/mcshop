<?php

namespace App\Models;


class FootPrint extends BaseModel
{
    //
    protected $table = 'footprint';

    public $timestamps = false;

    protected $casts = [
        'deleted' => 'boolean',
    ];

}
