<?php


namespace App;


class Constant
{
    const SEARCH_HISTORY_FROM_WX = 'wx';

    /**
     * 收藏
     */
    const COLLECT_GOOD_TYPE = 0;

    /**
     * 评论
     */
    const COMMENT_GOOD_TYPE = 0;

    /**
     * 优惠券
     */
    const COUPON_STATUS_NORMAL  = 0;
    const COUPON_TYPE_COMMON    = 0;
    const COUPON_TYPE_REGISTER  = 1;
    const COUPON_TYPE_CODE      = 2;
    const COUPON_STATUS_EXPIRED = 1;
    const COUPON_STATUS_OUT     = 2;
    const COUPON_TIME_TYPE_DAYS = 0;
    const COUPON_TIME_TYPE_TIME = 1;

    /**
     *用户优惠券
     */
    const COUPON_USER_STATUS_USABLE = 0;
    const COUPON_USER_STATUS_USED = 1;
    const COUPON_USER_STATUS_EXPIRED = 2;
    const COUPON_USER_STATUS_OUT = 3;

    /**
     *团购
     */
    const Groupon_RULE_STATUS_ON = 0;
    const Groupon_RULE_STATUS_DOWN_EXPIRE = 1;
    const Groupon_RULE_STATUS_DOWN_ADMIN = 2;
    const Groupon_STATUS_NONE = 0;
    const Groupon_STATUS_ON = 1;
    const Groupon_STATUS_SUCCEED = 2;
    const Groupon_STATUS_FAIL = 3;

}
