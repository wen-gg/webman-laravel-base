<?php

namespace Wengg\WebmanLaravelBase\App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 手机号
 * @author mosquito <zwj1206_hi@163.com>
 */
class MobileRule implements Rule
{
    public function passes($attribute, $value)
    {
        return is_string($value) && preg_match('/^1\d{10}$/', $value);
    }

    public function message()
    {
        return ':attribute 格式错误';
    }
}
