<?php


namespace App\Models;


class SearchHistory extends BaseModel
{
    protected $table = 'search_history';

    public $timestamps = false;

    protected $casts = [
        'deleted' => 'boolean'
    ];
}
