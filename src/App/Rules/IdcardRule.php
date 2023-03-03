<?php

namespace Wengg\WebmanLaravelBase\App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 身份证号
 * @author mosquito <zwj1206_hi@163.com>
 */
class IdcardRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (strlen($value) != 18) {
            return false;
        }
        return is_string($value) && preg_match('/^[1-9]\d{5}(?:18|19|20)\d{2}(?:0[1-9]|10|11|12)(?:0[1-9]|[1-2]\d|30|31)\d{3}[\dXx]$/', $value);
    }

    public function message()
    {
        return ':attribute 格式错误';
    }
}
