<?php

namespace WenGg\WebmanLaravelBase\Repositories;

/**
 * 仓库基类
 * @mixin \WenGg\WebmanLaravelBase\Models\BModel
 * @author mosquito <zwj1206_hi@163.com>
 */
abstract class BaseRepository
{
    /**
     * @var \WenGg\WebmanLaravelBase\Models\BModel
     * @author mosquito <zwj1206_hi@163.com>
     */
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->model, $name], $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([$this->model, $name], $arguments);
    }

    abstract public function getModel();
}
