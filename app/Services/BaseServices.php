<?php


namespace App\Services;

use App\CodeResponse;
use App\Exceptions\BusinessException;

class BaseServices
{
    protected static $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance instanceof static) {
            return self::$instance;
        }
        static::$instance = new static();
        return static::$instance;
    }


    /**
     * @param  array  $response
     * @param  null  $info
     * @throws BusinessException
     */
    public function throwBusinessException(array $response = CodeResponse::PARAM_ILLEGAL, $info = null)
    {
        if (!is_null($info)) {
            $response[1] = $info;
        }
        throw new BusinessException($response);
    }
}
