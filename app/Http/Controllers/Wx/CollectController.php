<?php

namespace App\Http\Controllers\Wx;

use App\Input\PageInput;
use App\Models\Collect;
use App\Services\CollectServices;
use App\Services\Goods\GoodsServices;


class CollectController extends WxController
{
    protected $only = [];

    public function getList()
    {
        $page             = PageInput::new();
        $userId           = $this->userId();
        $collectLists     = CollectServices::getInstance()->getList($page, $userId);
        $collectListItems = collect($collectLists->items());
        $collectValueIds  = $collectListItems->pluck('value_id')->toArray();
        $goodsList        = GoodsServices::getInstance()->getGoodsListByIds($collectValueIds)->keyBy('id');
        $collects         = $collectListItems->map(function (Collect $collect) use ($goodsList) {
            $collect_arr                = [];
            $good                       = $goodsList->get($collect->value_id);
            $collect_arr['id']          = $collect->id;
            $collect_arr['type']        = $collect->type;
            $collect_arr['valueId']     = $collect->value_id;
            $collect_arr['name']        = $good->name;
            $collect_arr['brief']       = $good->brief;
            $collect_arr['picUrl']      = $good->pic_url;
            $collect_arr['retailPrice'] = $good->retail_price;
            return $collect_arr;

        });
        return $this->successPaginate($collectLists, $collects);
    }

    public function addOrDelete()
    {
        $type    = $this->verifyInteger('type', 0);
        $valueId = $this->verifyInteger('valueId');
        $userId  = $this->userId();
        CollectServices::getInstance()->addOrDelete($userId, $type, $valueId);
        return $this->success();

    }
}
