<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Models\Goods\Goods;
use App\Models\Promotion\GrouponRules;
use App\Services\Goods\BrandServices;
use App\Services\Goods\GoodsServices;
use App\Services\Promotion\GrouponServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GrouponController extends WxController
{
    public $except = ['test'];

    public function test() {
        $groupRule = GrouponServices::getInstance()->getGrouponRuleById(1);
        $resp = GrouponServices::getInstance()->createGroupShareImage($groupRule);
        return response()->make($resp)->header('Content-Type', 'image/png');
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 获取团购列表
     */
    public function list()
    {
        $page           = PageInput::new();
        $groupRuleLists = GrouponServices::getInstance()->getGroupRuleLists($page);
        $groupRuleList  = $groupRuleLists->items();
        $groupRuleList  = collect($groupRuleList);
        $goodIds        = $groupRuleList->pluck('goods_id')->toArray();
        $goods          = GoodsServices::getInstance()->getGoodsListByIds($goodIds)->keyBy('id');
        $list           = $groupRuleList->map(function (GrouponRules $rule) use ($goods) {
            $good = $goods->get($rule->goods_id);
            return [
                'id'              => $good->id,
                'name'            => $good->name,
                'brief'           => $good->brief,
                'picUrl'          => $good->pic_url,
                'counterPrice'    => $good->counter_price,
                'retailPrice'     => $good->retail_price,
                'grouponPrice'    => bcsub($good->retail_price, $rule->discount, 2),
                'grouponDiscount' => $rule->discount,
                'grouponMember'   => $rule->discount_member,
                'expireTime'      => $rule->expire_time,
            ];
        });
        $data           = $this->paginate($groupRuleLists, $list);
        return $this->success($data);
    }
}
