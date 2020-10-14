<?php


namespace App\Http\Controllers\Wx;


use App\Http\Controllers\Controller;
use App\Http\Services\UserServices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //TODO 获取参数
        $username = $request->input('username', '');
        $password = $request->input('password', '');
        $mobile   = $request->input('mobile', '');
        $code     = $request->input('code', '');

        if (empty($username) || empty($password) || empty($mobile) || empty($code)) {
            return ['error' => 401, 'errmsg' => '参数错误'];
        }

        $user = (new UserServices())->getByUsername($username);

        if (!is_null($user)) {
            return ['error' => 704, 'errmsg' => '用户名已经注册'];
        }

        $validate = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$']);

        if ($validate->failed()) {
            return ['error' => 707, 'errmsg' => '手机号格式不正确'];
        }

        $user = (new UserServices())->getByMobile($mobile);

        if (!is_null($user)) {
            return ['error' => 705, 'errmsg' => '手机号码已注册'];
        }

        $avatarUrl = "https://yanxuan.nosdn.127.net/80841d741d7fa3073e0ae27bf487339f.jpg?imageView&quality=90&thumbnail=64x64";
        //TODO 验证验证码是否正确
        $user                  = new User();
        $user->username        = $username;
        $user->password        = Hash::make($password);
        $user->mobile          = $mobile;
        $user->avatar          = $avatarUrl;
        $user->nickname        = $username;
        $user->last_login_time = Carbon::now()->toDateTimeString();
        $user->last_login_ip   = $request->getClientIp();
        $user->save();

        //TODO 新用户发券

        //TODO 返回用户信息和token
        return [
            'error' => 0, 'errmsg' => 'ok', 'data' => [
                'token' => '124',
                'userinfo' => [
                    'nickname' => $username,
                    'avatar'   => $avatarUrl
                ]
            ]
        ];
    }
}
