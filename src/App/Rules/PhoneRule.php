<?php

namespace Wengg\WebmanLaravelBase\App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 联系电话
 * @author mosquito <zwj1206_hi@163.com>
 */
class PhoneRule implements Rule
{
    public function passes($attribute, $value)
    {
        return is_string($value) && preg_match('/^\d{3}-?\d{8}$|^\d{4}-?\d{7}$/', $value);
    }

    public function message()
    {
        return ':attribute 格式错误';
    }
}
