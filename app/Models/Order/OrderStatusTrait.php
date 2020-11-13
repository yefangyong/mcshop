<?php

namespace App\Models\Order;

use App\Constant;



trait OrderStatusTrait
{
    public function canCancelHandle()
    {
        return $this->order_status == Constant::ORDER_STATUS_CREATE;
    }
}
