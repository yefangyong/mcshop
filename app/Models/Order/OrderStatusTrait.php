<?php

namespace App\Models\Order;

use App\Constant;
use Exception;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Boolean;

/* @method Boolean canCancelHandle
 * @method Boolean canDeleteHandle
 * @method Boolean canPayHandle
 * @method Boolean canCommentHandle
 * @method Boolean canConfirmHandle
 * @method Boolean canRefundHandle
 * @method Boolean canAftersaleHandle
 * @method Boolean canRebuyHandle
 */
trait OrderStatusTrait
{
    private $canHandleMap = [
        // 取消操作
        'cancel'      => [
            Constant::ORDER_STATUS_CREATE
        ],
        // 删除操作
        'delete'      => [
            Constant::ORDER_STATUS_CANCEL,
            Constant::ORDER_STATUS_AUTO_CANCEL,
            Constant::ORDER_STATUS_ADMIN_CANCEL,
            Constant::ORDER_STATUS_REFUND_CONFIRM,
            Constant::ORDER_STATUS_CONFIRM,
            Constant::ORDER_STATUS_AUTO_CONFIRM
        ],
        // 支付操作
        'pay'         => [
            Constant::ORDER_STATUS_CREATE
        ],
        // 发货
        'ship'        => [
            Constant::ORDER_STATUS_PAY
        ],
        // 评论操作
        'comment'     => [
            Constant::ORDER_STATUS_CONFIRM,
            Constant::ORDER_STATUS_AUTO_CONFIRM
        ],
        // 确认收货操作
        'confirm'     => [Constant::ORDER_STATUS_SHIP],
        // 取消订单并退款操作
        'refund'      => [Constant::ORDER_STATUS_PAY],
        // 再次购买
        'rebuy'       => [
            Constant::ORDER_STATUS_CONFIRM,
            Constant::ORDER_STATUS_AUTO_CONFIRM
        ],
        // 售后操作
        'aftersale'   => [
            Constant::ORDER_STATUS_CONFIRM,
            Constant::ORDER_STATUS_AUTO_CONFIRM
        ],
        // 同意退款
        'agreerefund' => [
            Constant::ORDER_STATUS_REFUND
        ],
    ];

    public function __call($name, $arguments)
    {
        if (Str::is('can*Handle', $name)) {
            if (is_null($this->order_status)) {
                throw new Exception("order status is null where call method{$name}!");
            }
            $key = Str::of($name)->replaceFirst('can', '')->replaceLast('Handle', '')->lower();
            return in_array($this->order_status, $this->canHandleMap[(string) $key]);
        } elseif (Str::is('is*Status', $name)) {
            if (is_null($this->order_status)) {
                throw new Exception("order status is null where call method{$name}!");
            }
            $key    = Str::of($name)->replaceFirst('is', '')->replaceLast('Status',
                '')->snake()->upper()->prepend('ORDER_STATUS');
            $status = (new \ReflectionClass(Constant::class))->getConstant($key);
            return $this->order_status == $status;
        }

        return parent::__call($name, $arguments);
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
