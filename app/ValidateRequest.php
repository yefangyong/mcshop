<?php


namespace App;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait ValidateRequest
{

    /**
     * @param $key
     * @param  null  $default
     * @return array|mixed|null
     * @throws BusinessException
     * 验证数组
     */
    public function verifyArrayNotEmpty($key, $default = null)
    {
        return $this->verifyData($key, $default, 'array|min:1');
    }

    /**
     * @param $key
     * @param  null  $default
     * @return array|mixed|null
     * @throws BusinessException
     */
    public function verifyId($key, $default = null)
    {
        return $this->verifyData($key, $default, 'integer | digits_between:1,20 | min:1');
    }

    /**
     * @param $key
     * @param  null  $default
     * @return array|mixed|null
     * @throws BusinessException
     */
    public function verifyString($key, $default = null)
    {
        return $this->verifyData($key, $default, 'string');
    }

    /**
     * @param $key
     * @param $default
     * @return array|mixed|null
     * @throws BusinessException
     */
    public function verifyInteger($key, $default = null)
    {
        return $this->verifyData($key, $default, 'integer');
    }

    /**
     * @param $key
     * @param  null  $default
     * @param  array  $enum
     * @return array|mixed|null
     * @throws BusinessException
     */
    public function verifyEnum($key, $default = null, $enum = [])
    {
        return $this->verifyData($key, $default, Rule::in($enum));
    }

    /**
     * @param $key
     * @param  null  $default
     * @return array|mixed|null
     * @throws BusinessException
     */
    public function verifyBoolean($key, $default = null)
    {
        return $this->verifyData($key, $default, 'boolean');
    }


    /**
     * @param $key
     * @param $default
     * @param $rule
     * @return array|mixed|null
     * @throws BusinessException
     */
    private function verifyData($key, $default, $rule)
    {
        $value    = request()->input($key, $default);
        $validate = Validator::make([$key => $value], [$key => $rule]);
        if (is_null($value) && is_null($default)) {
            return null;
        }
        if ($validate->fails()) {
            throw new BusinessException(CodeResponse::PARAM_NOT_EMPTY);
        }
        return $value;
    }
}
