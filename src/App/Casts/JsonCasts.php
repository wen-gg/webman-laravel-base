<?php

namespace Wengg\WebmanLaravelBase\App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Json类型转换
 * @author mosquito <zwj1206_hi@163.com>
 */
class JsonCasts implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return json_decode($value, true);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
