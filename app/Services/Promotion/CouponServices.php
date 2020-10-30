<?php


namespace App\Services\Promotion;


use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Models\Promotion\Coupon;
use App\Models\Promotion\CouponUser;
use App\Services\BaseServices;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CouponServices extends BaseServices
{

    /**
     * @param $couponId
     * @param $userId
     * @throws BusinessException
     * 用户领取消费券
     */
    public function receive($couponId, $userId)
    {
        $coupon = $this->getCoupon($couponId);

        if (is_null($coupon)) {
            $this->throwBusinessException(CodeResponse::SYSTEM_ERROR);
        }

        //当前已领取的数量和总数量比较
        $total        = $coupon->total;
        $totalCoupons = $this->getCouponUserTotalByCouponId($couponId);
        if ($total != 0 && $totalCoupons >= $total) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT);
        }

        //当前用户已领取数量和用户限领取数量进行比较
        $limit           = $coupon->limit;
        $userCouponCount = $this->getUserCouponsCount($couponId, $userId);
        if ($limit != 0 && $userCouponCount >= $limit) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '优惠券已经领取过');
        }

        //优惠券分发类型，比如注册赠券类型的优惠券不能领取
        $type = $coupon->type;
        if ($type == Constant::COUPON_TYPE_REGISTER) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '新用户优惠券自动发送');
        } elseif ($type == Constant::COUPON_TYPE_CODE) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '优惠券只能兑换');
        } elseif ($type != Constant::COUPON_TYPE_COMMON) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '优惠券类型不支持');
        }

        //优惠券的状态，已经过期或者下架
        $status = $coupon->status;
        if ($status == Constant::COUPON_STATUS_EXPIRED) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '优惠券已经过期');
        } elseif ($status == Constant::COUPON_STATUS_OUT) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '优惠券已领完');
        }

        //用户领券记录
        $timeType = $coupon->time_type;
        if ($timeType == Constant::COUPON_TIME_TYPE_TIME) {
            $start_time = $coupon->start_time;
            $end_time   = $coupon->end_time;
        } elseif ($timeType == Constant::COUPON_TIME_TYPE_DAYS) {
            $start_time = \Illuminate\Support\Carbon::now()->toDateTimeString();
            $end_time   = date("Y-m-d H:i:s", time() + $coupon->days * 24 * 3600);
        }
        $this->saveCouponUser($userId, $couponId, $start_time, $end_time);
    }

    /**
     * @param $userId
     * @param $couponId
     * @param $start_time
     * @param $end_time
     * 记录用户领券
     */
    public function saveCouponUser($userId, $couponId, $start_time, $end_time)
    {
        $couponUser              = CouponUser::new();
        $couponUser->user_id     = $userId;
        $couponUser->coupon_id   = $couponId;
        $couponUser->start_time  = $start_time;
        $couponUser->end_time    = $end_time;
        $couponUser->add_time    = Carbon::now()->toDateTimeString();
        $couponUser->update_time = Carbon::now()->toDateTimeString();
        $couponUser->save();
    }

    /**
     * @param $couponId
     * @param $userId
     * @return int
     * 获取用户领取某种优惠券的数量
     */
    public function getUserCouponsCount($couponId, $userId)
    {
        return CouponUser::query()->where([
            'deleted' => 0, 'coupon_id' => $couponId, 'user_id' => $userId
        ])->count('id');
    }

    /**
     * @return int
     */
    public function getCouponUserTotalByCouponId($couponId)
    {
        return CouponUser::query()->where(['deleted' => 0, 'coupon_id' => $couponId])->count('id');
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     * 根据优惠券Id获取优惠券
     */
    public function getCoupon($id)
    {
        return Coupon::query()->find($id);
    }


    /**
     * @param  PageInput  $page
     * @param  string[]  $column
     * @return LengthAwarePaginator
     * 获取优惠列表
     */
    public function getCouponList(PageInput $page, $column = ['*'])
    {
        return Coupon::query()->select($column)->where('type',
            Constant::COUPON_TYPE_COMMON)->where('status',
            Constant::COUPON_STATUS_NORMAL)->orderBy($page->sort, $page->order)->paginate($page->limit, $column, 'page',
            $page->page);
    }

    /**
     * @param  PageInput  $page
     * @param $status
     * @param $userId
     * @param  string[]  $column
     * @return LengthAwarePaginator
     * 获取用户的优惠券
     */
    public function getMyCouponList(PageInput $page, $status, $userId, $column = ['*'])
    {
        return CouponUser::query()->where('user_id', $userId)->where('status',
            $status)->orderBy($page->sort, $page->order)->paginate($page->limit, $column, 'page', $page->page);
    }

    /**
     * @param $ids
     * @return Builder[]|Collection
     * 获取优惠券
     */
    public function getCouponsByIds($ids)
    {
        return Coupon::query()->whereIn('id', $ids)->get();
    }
}
