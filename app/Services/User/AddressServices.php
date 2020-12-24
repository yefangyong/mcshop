<?php


namespace App\Services\User;


use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Input\AddressSaveInput;
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
     * @return Address[]|Builder[]|Collection
     * 获取用户地址
     */
    public function getAddressByUserId($userId)
    {
        return Address::query()->whereUserId($userId)->get();
    }

    /**
     * @return bool
     * @throws BusinessException
     * 重置用户地址
     */
    public function resetDefaultAddress()
    {
        if (!Address::query()->update(['is_default' => 0])) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }
        return true;
    }

    /**
     * @param $userId
     * @param  AddressSaveInput  $input
     * @return Address|Builder|Model|object|null
     * @throws BusinessException
     * 保存地址
     */
    public function saveAddress($userId, AddressSaveInput $input)
    {
        if (!is_null($input->id)) {
            $address = $this->getUserAddress($userId, $input->id);
        } else {
            $address          = Address::new();
            $address->user_id = $userId;
        }

        if ($input->isDefault) {
            $this->resetDefaultAddress();
        }

        $address->address_detail = $input->addressDetail;
        $address->area_code      = $input->areaCode;
        $address->city           = $input->city;
        $address->county         = $input->county;
        $address->is_default     = $input->isDefault;
        $address->name           = $input->name;
        $address->postal_code    = $input->postalCode;
        $address->province       = $input->province;
        $address->tel            = $input->tel;
        $address->save();
        return $address;
    }

    /**
     * @param $userId
     * @param $id
     * @throws BusinessException
     * 删除地址
     */
    public function delete($userId, $id)
    {
        $address = $this->getUserAddress($userId, $id);
        if (is_null($address)) {
            $this->throwBusinessException();
        }
        Address::query()->where('id', $id)->where('user_id', $userId)->delete();
    }

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
