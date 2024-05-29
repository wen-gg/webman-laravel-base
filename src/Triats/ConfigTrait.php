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
     * @return array|string
     * @author mosquito <zwj1206_hi@163.com>
     */
    protected static function configFormat(array $arr, string $key = null, bool $transform = false)
    {
        $result = null;
        if (is_null($key)) {
            if ($transform === true) {
                $result = [];
                foreach ($arr as $k => $v) {
                    $result[] = [
                        'key' => $k,
                        'value' => $v,
                    ];
                }
            } else {
                $result = $arr;
            }
        } else {
            $result = $arr[$key];
        }
        return $result;
    }
}
