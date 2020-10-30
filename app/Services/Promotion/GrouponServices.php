<?php


namespace App\Services\Promotion;


use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Models\Promotion\Coupon;
use App\Models\Promotion\CouponUser;
use App\Models\Promotion\GrouponRules;
use App\Services\BaseServices;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class GrouponServices extends BaseServices
{
    /**
     * @param  PageInput  $page
     * @param  string[]  $column
     * @return LengthAwarePaginator
     * 获取团购规则列表数据
     */
    public function getGroupRuleLists(PageInput $page, $column = ['*'])
    {
        return GrouponRules::query()->where('status', Constant::Groupon_RULE_STATUS_ON)->orderBy($page->sort,
            $page->order)->paginate($page->limit, $column, 'page', $page->page);
    }
}
