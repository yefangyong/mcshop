<?php


namespace App\Services;


use App\Constant;
use App\Input\PageInput;
use App\Models\Collect;
use Illuminate\Support\Facades\Date;

class CollectServices extends BaseServices
{
    public function getGoodsCollect($goodIds)
    {
        return Collect::query()->where('type', Constant::COLLECT_GOOD_TYPE)->where('value_id', $goodIds)->count('id');
    }

    public function getList(PageInput $page, $userId, $columns = ['*'])
    {
        return Collect::query()->whereUserId($userId)->paginate($page->limit, $columns, 'page', $page->page);
    }

    public function addOrDelete($userId, $type, $valuedId)
    {
        $where   = [
            'user_id'  => $userId,
            'type'     => $type,
            'value_id' => $valuedId
        ];
        $collect = Collect::query()->where($where)->get()->toArray();
        if (!empty($collect)) {
            return Collect::query()->where($where)->delete();
        } else {
            $collect           = Collect::new();
            $collect->type     = $type;
            $collect->value_id = $valuedId;
            $collect->user_id  = $userId;
            $collect->add_time = Date::now();
            return $collect->save();
        }
    }

}
