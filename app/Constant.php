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
    const COUPON_USER_STATUS_USABLE  = 0;
    const COUPON_USER_STATUS_USED    = 1;
    const COUPON_USER_STATUS_EXPIRED = 2;
    const COUPON_USER_STATUS_OUT     = 3;

    /**
     *团购
     */
    const Groupon_RULE_STATUS_ON          = 0;
    const Groupon_RULE_STATUS_DOWN_EXPIRE = 1;
    const Groupon_RULE_STATUS_DOWN_ADMIN  = 2;
    const Groupon_STATUS_NONE             = 0;
    const Groupon_STATUS_ON               = 1;
    const Groupon_STATUS_SUCCEED          = 2;
    const Groupon_STATUS_FAIL             = 3;

    /**
     * 订单状态
     */
    const ORDER_STATUS_CREATE          = 101;
    const ORDER_STATUS_PAY             = 201;
    const ORDER_STATUS_SHIP            = 301;
    const ORDER_STATUS_CONFIRM         = 401;
    const ORDER_STATUS_CANCEL          = 102;
    const ORDER_STATUS_AUTO_CANCEL     = 103;
    const ORDER_STATUS_ADMIN_CANCEL    = 104;
    const ORDER_STATUS_REFUND          = 202;
    const ORDER_STATUS_REFUND_CONFIRM  = 203;
    const ORDER_STATUS_GROUPON_TIMEOUT = 204;
    const ORDER_STATUS_AUTO_CONFIRM    = 402;

    const ORDER_STATUS_TEXT_MAP = [
        self::ORDER_STATUS_CREATE          => '未付款',
        self::ORDER_STATUS_CANCEL          => '已取消',
        self::ORDER_STATUS_AUTO_CANCEL     => '已取消(系统)',
        self::ORDER_STATUS_ADMIN_CANCEL    => '已取消(管理员)',
        self::ORDER_STATUS_PAY             => '已付款',
        self::ORDER_STATUS_REFUND          => '订单取消，退款中',
        self::ORDER_STATUS_REFUND_CONFIRM  => '已退款',
        self::ORDER_STATUS_GROUPON_TIMEOUT => '已超时团购',
        self::ORDER_STATUS_SHIP            => '已发货',
        self::ORDER_STATUS_CONFIRM         => '已收货',
        self::ORDER_STATUS_AUTO_CONFIRM    => '已收货(系统)'
    ];

    const ORDER_SHOW_TYPE_ALL           = 0;//全部订单
    const ORDER_SHOW_TYPE_WAIT_PAY      = 1;//待付款订单
    const ORDER_SHOW_TYPE_WAIT_DELIVERY = 2;//待发货订单
    const ORDER_SHOW_TYPE_WAIT_RECEIPT  = 3;//待收货订单
    const ORDER_SHOW_TYPE_WAIT_COMMENT  = 4;//待评价订单

    const ORDER_SHOW_TYPE_STATUS_MAP = [
        self::ORDER_SHOW_TYPE_ALL           => [],
        self::ORDER_SHOW_TYPE_WAIT_PAY      => [self::ORDER_STATUS_CREATE],
        self::ORDER_SHOW_TYPE_WAIT_DELIVERY => [self::ORDER_STATUS_PAY],
        self::ORDER_SHOW_TYPE_WAIT_RECEIPT  => [self::ORDER_STATUS_SHIP],
        self::ORDER_SHOW_TYPE_WAIT_COMMENT  => [self::ORDER_STATUS_CONFIRM],
    ];

}
