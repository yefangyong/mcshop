<?php

namespace App\Http\Controllers\Wx;

use App\Constant;
use App\Models\Order\Order;
use App\Services\Order\OrderServices;


class UserController extends WxController
{
    protected $only = [];

    public function index()
    {
        $userId    = $this->userId();
        $orders    = OrderServices::getInstance()->getOrdersByUserId($userId);
        $unpaid    = 0;
        $unship    = 0;
        $unrecv    = 0;
        $uncomment = 0;

        $orders->map(function (Order $order) use (&$unpaid, &$unrecv, &$uncomment, &$unship) {
            switch ($order->order_status) {
                case Constant::ORDER_STATUS_CREATE:
                    $unpaid++;
                    break;
                case Constant::ORDER_STATUS_PAY:
                    $unship++;
                    break;
                case Constant::ORDER_STATUS_SHIP:
                    $unrecv++;
                    break;
                case Constant::ORDER_STATUS_CONFIRM:
                    $uncomment++;
                    break;
                default:
            }
        });
        return $this->success(['order' => compact('uncomment', 'unrecv', 'unship', 'unpaid')]);
    }
}
