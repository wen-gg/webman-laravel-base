<?php

namespace Wengg\WebmanLaravelBase\App\Triats;

trait Instance
{
    /**
     * @var static
     */
    protected static $_instance = null;

    /**
     * 实例
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function instance()
    {
        if (is_null(static::$_instance)) {
            static::$_instance = static::instanceNew(...func_get_args());
        }
        return static::$_instance;
    }

    /**
     * 新建实例
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function instanceNew()
    {
        return new static(...func_get_args());
    }

    /**
     * 实例销毁
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function instanceDestroy()
    {
        if (!is_null(static::$_instance)) {
            static::$_instance = null;
        }
    }
}
