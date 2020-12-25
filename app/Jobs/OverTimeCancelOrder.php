<?php

namespace App\Jobs;

use App\Exceptions\BusinessException;
use App\Services\Order\OrderServices;
use App\Services\SystemServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OverTimeCancelOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;
    private $orderId;

    /**
     * OverTimeCancelOrder constructor.
     * @param $userId
     * @param $orderId
     */
    public function __construct($userId, $orderId)
    {
        $this->userId  = $userId;
        $this->orderId = $orderId;
        $time          = SystemServices::getInstance()->getUnPidTime();
        $this->delay(now()->addMinutes($time));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            OrderServices::getInstance()->systemCancel($this->userId, $this->orderId);
        } catch (BusinessException $e) {
            Log::error($e->getMessage());
        }
    }
}
