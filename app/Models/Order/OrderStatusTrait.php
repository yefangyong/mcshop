<?php

namespace App\Models\Order;

use App\Constant;


trait OrderStatusTrait
{
    public function canCancelHandle()
    {
        return $this->order_status == Constant::ORDER_STATUS_CREATE;
    }

    public function canPayHandle()
    {
        return $this->order_status == Constant::ORDER_STATUS_CREATE;
    }

    public function canRefundHandle()
    {
        return $this->order_status == Constant::ORDER_STATUS_PAY;
    }

    public function canAgreeRefundHandle()
    {
        return $this->order_status == Constant::ORDER_STATUS_REFUND;
    }

    public function canShipHandle()
    {
        return $this->order_status == Constant::ORDER_STATUS_PAY;
    }

    public function canConfirmHandle()
    {
        return $this->order_status == Constant::ORDER_STATUS_SHIP;
    }

    public function canCommentHandle()
    {
        return in_array($this->order_status, [Constant::ORDER_STATUS_CONFIRM, Constant::ORDER_STATUS_AUTO_CONFIRM]);
    }

    public function canAftersaleHandle()
    {
        return in_array($this->order_status, [Constant::ORDER_STATUS_CONFIRM, Constant::ORDER_STATUS_AUTO_CONFIRM]);
    }

    public function canDeleteHandle()
    {
        return in_array($this->order_status, [
            Constant::ORDER_STATUS_CANCEL,
            Constant::ORDER_STATUS_AUTO_CANCEL,
            Constant::ORDER_STATUS_REFUND,
            Constant::ORDER_STATUS_REFUND_CONFIRM,
            Constant::ORDER_STATUS_ADMIN_CANCEL,
            Constant::ORDER_STATUS_CONFIRM
        ]);
    }

    public function canRebuyHandle()
    {
        return in_array($this->order_status, [Constant::ORDER_STATUS_CONFIRM, Constant::ORDER_STATUS_AUTO_CONFIRM]);
    }

    public function getCanHandleOptions()
    {
        return [
            'cancel'    => $this->canCancelHandle(),
            'delete'    => $this->canDeleteHandle(),
            'pay'       => $this->canPayHandle(),
            'comment'   => $this->canCommentHandle(),
            'confirm'   => $this->canConfirmHandle(),
            'refund'    => $this->canRefundHandle(),
            'aftersale' => $this->canAftersaleHandle(),
            'rebuy'     => $this->canRebuyHandle()
        ];
    }
}
