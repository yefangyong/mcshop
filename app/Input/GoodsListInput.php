<?php


namespace App\Input;


use Illuminate\Validation\Rule;

class GoodsListInput extends Input
{
    public $categoryId;
    public $brandId;
    public $keyword;
    public $isNew;
    public $isHot;
    public $page = 1;
    public $limit = 10;
    public $sort = 'add_time';
    public $order = 'desc';


    public function rule()
    {
        return [
            'categoryId' => 'integer',
            'brandId'    => 'integer',
            'keyword'    => 'string',
            'isNew'      => 'boolean',
            'isHot'      => 'boolean',
            'page'       => 'integer',
            'limit'      => 'integer',
            'sort'       => Rule::in(['add_time', 'retail_price', 'name']),
            'order'      => Rule::in(['desc', 'asc'])
        ];
    }

}
