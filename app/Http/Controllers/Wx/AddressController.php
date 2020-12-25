<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Input\AddressSaveInput;
use App\Services\User\AddressServices;
use Illuminate\Http\JsonResponse;


class AddressController extends WxController
{
    /**
     * @return JsonResponse
     * 地址列表
     */
    public function list()
    {
        $userId = $this->user()->id;
        $list   = AddressServices::getInstance()->getAddressByUserId($userId);
        return $this->success([
            'total' => $list->count(),
            'page'  => 1,
            'list'  => $list->toArray(),
            'pages' => 1
        ]);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 地址详情
     */
    public function detail()
    {
        $id      = $this->verifyId('id', 0);
        $address = AddressServices::getInstance()->getUserAddress($this->userId(), $id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }
        return $this->success($address);
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 更新或者新增地址
     */
    public function save()
    {
        $userId = $this->userId();
        $input  = AddressSaveInput::new();
        AddressServices::getInstance()->saveAddress($userId, $input);
        return $this->success();
    }

    /**
     * @return JsonResponse
     * @throws BusinessException
     * 删除地址
     */
    public function delete()
    {
        $id = $this->verifyInteger('id');
        AddressServices::getInstance()->delete($this->userId(), $id);
        return $this->success();
    }
}
