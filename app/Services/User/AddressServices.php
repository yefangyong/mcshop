<?php


namespace App\Services\User;


use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Models\User\Address;
use App\Models\User\User;
use App\Notifications\VerificationCode;
use App\Services\BaseServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Overtrue\EasySms\PhoneNumber;

class AddressServices extends BaseServices
{
    /**
     * @param $userId
     * @param  null  $addressId
     * @return Address|Builder|Model|object|null
     * 获取用户地址或者默认的地址
     */
    public function getAddressOrDefault($userId, $addressId = null)
    {
        if (empty($addressId)) {
            $address = $this->getDefaultAddress($userId);
        } else {
            $address = $this->getUserAddress($userId, $addressId);
            if (empty($address)) {
                $this->throwBusinessException(CodeResponse::SYSTEM_ERROR);
            }
        }
        return $address;
    }

    public function getUserAddress($userId, $addressId)
    {
        return Address::query()->whereUserId($userId)->whereId($addressId)->first();
    }

    public function getDefaultAddress($userId)
    {
        return Address::query()->whereUserId($userId)->where('is_default', 1)->first();
    }
}
