<?php


namespace App\Services\User;


use App\CodeResponse;
use App\Exceptions\BusinessException;
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

class UserServices extends BaseServices
{
    /**
     * @param $ids
     * @return Builder[]|Collection|\Illuminate\Support\Collection
     * 获取用户
     */
    public function getUsers($ids) {
        if (empty($ids))
            return collect([]);
        return User::query()->where('deleted', 0)->whereIn('id',$ids)->get();
    }
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

    /**
     * @param $mobile
     * @param $send_count
     * @return bool
     * 检查验证码每天发送的次数
     */
    public function checkMobileSendCaptchaCount($mobile, $send_count)
    {
        $countKey = 'register_captcha_count_'.$mobile;

        if (Cache::has($countKey)) {
            $count = Cache::increment('register_captcha_count_'.$mobile, 1);
            if ($count > $send_count) {
                return false;
            }
        } else {
            Cache::put($countKey, 1, Carbon::tomorrow()->diffInSeconds(now()));
        }

        return true;
    }

    /**
     * @param  string  $mobile
     * @param $code
     * @return bool
     * 发送验证码
     */
    public function sendCaptchaMsg(string $mobile, $code)
    {
//        if (app()->env == 'testing') {
//            Log::info('手机号码：'.$mobile.'不用发送短信哦');
//            return true;
//        }
        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($mobile, 86)
        )->notify(new VerificationCode($code, 'SMS_117526525'));
        return true;
    }

    /**
     * @param $mobile
     * @param $code
     * @return bool
     * @throws BusinessException
     * 检查验证码
     */
    public function checkCaptcha($mobile, $code)
    {
        $key    = 'register_captcha_'.$mobile;
        $isPass = $code == Cache::get($key);
        if ($isPass) {
            Cache::forget($key);
            return true;
        } else {
            throw new BusinessException(CodeResponse::AUTH_CAPTCHA_UNMATCH);
        }
    }

    /**
     * @param  string  $mobile
     * @return int
     * @throws \Exception
     * 设置短信验证码
     */
    public function setCaptcha(string $mobile)
    {
        $code = random_int(100000, 999999);
        Cache::put('register_captcha_'.$mobile, $code, 600);
        return $code;
    }
}
