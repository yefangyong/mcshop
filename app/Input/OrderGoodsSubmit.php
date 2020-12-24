<?php


namespace App\Input;


class OrderGoodsSubmit extends Input
{
    public $cartId;
    public $addressId;
    public $couponId;
    public $userCouponId;
    public $message;
    public $grouponRulesId;
    public $grouponLinkId;


    public function rule()
    {
        return [
            //'cartId'         => 'required|integer',
            //'addressId'      => 'required|integer',
            'couponId'       => 'integer',
            'userCouponId'   => 'integer',
            'message'        => 'string',
            'grouponRulesId' => 'integer',
            'grouponLinkId'  => 'integer',
        ];
    }

}
