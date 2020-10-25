<?php


namespace App\Services;


use App\Constant;
use App\Models\Collect;

class CollectServices extends BaseServices
{
   public function getGoodsCollect($goodIds)
   {
       return Collect::query()->where('type', Constant::COLLECT_GOOD_TYPE)->where('deleted', 0)->where('value_id', $goodIds)->count('id');
   }

}
