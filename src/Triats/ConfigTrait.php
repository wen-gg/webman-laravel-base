<?php

namespace WenGg\WebmanLaravelBase\Triats;

/**
 * 配置复用
 * @author mosquito <zwj1206_hi@163.com>
 */
trait ConfigTrait
{
    /**
     * 配置格式化
     * @author mosquito <zwj1206_hi@163.com>
     * @deprecated v1.0.1
     */
    protected static function configFormat(array $arr, $key = null, bool $transform = false)
    {
        return static::configFormatNew($arr, $key, $transform === true ? 'key' : null, $transform === true ? 'value' : null);
    }

    /**
     * 配置格式化
     * @author mosquito <zwj1206_hi@163.com>
     */
    protected static function configFormatNew(array $arr, $key = null, string $keyName = null, string $valueName = null)
    {
        $result = null;
        if (is_null($key)) {
            if ($keyName !== null && $valueName !== null) {
                $result = [];
                foreach ($arr as $k => $v) {
                    $result[] = [
                        $keyName => $k,
                        $valueName => $v,
                    ];
                }
            } else {
                $result = $arr;
            }
        } elseif (is_array($key)) {
            $result = [];
            if ($keyName !== null && $valueName !== null) {
                foreach ($key as $k) {
                    $result[] = [
                        $keyName => $k,
                        $valueName => $arr[$k],
                    ];
                }
            } else {
                foreach ($key as $k) {
                    $result[$k] = $arr[$k];
                }
            }
        } elseif (is_int($key) || is_string($key)) {
            $result = $arr[$key];
        }
        return $result;
    }
}
