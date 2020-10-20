<?php


namespace App\Services;


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
}
