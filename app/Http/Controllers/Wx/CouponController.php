<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Models\Promotion\CouponUser;
use App\Services\Promotion\CouponServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;


class CouponController extends WxController
{
    protected $except = ['list'];

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 获取用户的优惠券列表
     */
    public function myList()
    {
        $pageInput      = PageInput::new();
        $status         = $this->verifyInteger('status', 0);
        $couponList     = CouponServices::getInstance()->getMyCouponList($pageInput, $status, $this->userId());
        $couponUserList = collect($couponList->items());
        $couponIds      = $couponUserList->pluck('coupon_id')->toArray();
        $coupons        = CouponServices::getInstance()->getCouponsByIds($couponIds)->keyBy('id');
        $myList         = $couponUserList->map(function (CouponUser $items) use ($coupons) {
            $coupon = $coupons->get($items->coupon_id);
            return [
                'id'        => $items->id,
                'cid'       => $coupon->id,
                'name'      => $coupon->name,
                'desc'      => $coupon->desc,
                'tag'       => $coupon->tag,
                'min'       => $coupon->min,
                'discount'  => $coupon->discount,
                'startTime' => $items->start_time,
                'endTime'   => $items->end_time,
                'available' => false
            ];
        });
        $list           = $this->paginate($couponList, $myList);
        return $this->success($list);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 用户领券
     */
    public function receive()
    {
        $couponId = $this->verifyInteger('couponId');
        $userId   = $this->userId();
        CouponServices::getInstance()->receive($couponId, $userId);
        return $this->success();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     */
    public function list()
    {
        $pageInput = PageInput::new();
        $column    = ['id', 'name', 'desc', 'discount', 'tag', 'min', 'days'];
        $list      = CouponServices::getInstance()->getCouponList($pageInput, $column);
        return $this->successPaginate($list);
    }
}
