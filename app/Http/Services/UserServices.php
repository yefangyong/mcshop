<?php


namespace App\Http\Services;


use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserServices
{
    /**
     * 根据用户名返回用户信息
     * @param $username
     * @return Builder|Model|object|null
     */
    public function getByUsername($username)
    {
        return User::query()->where('username', $username)->where('deleted', 0)->first();
    }

    /**
     * 根据手机号码返回用户信息
     * @param $mobile
     * @return Builder|Model|object|null
     */
    public function getByMobile($mobile)
    {
        return User::query()->where('mobile', $mobile)->where('deleted', 0)->first();
    }
}
