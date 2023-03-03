<?php

namespace Wengg\WebmanLaravelBase\App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * 数组类型转换
 * @author mosquito <zwj1206_hi@163.com>
 */
class ArrayCasts implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return json_decode($value, true) ?: [];
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode(array_values(is_array($value) ? $value : []), JSON_UNESCAPED_UNICODE);
    }
}
