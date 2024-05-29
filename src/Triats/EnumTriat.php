<?php

namespace WenGg\WebmanLaravelBase\Triats;

trait EnumTriat
{
    public static function values()
    {
        $args = func_get_args();
        $cases = $args ? (is_array(current($args)) ? current($args) : $args) : static::cases();
        $result = [];
        foreach ($cases as $val) {
            if (!$val instanceof static) {
                continue;
            }
            $result[] = $val->value;
        }
        return $result;
    }

    public static function valueToItems($values)
    {
        $cases = static::cases();
        $result = [];
        foreach ($cases as $val) {
            if (in_array($val->value, (array)$values)) {
                $result[] = $val;
            }
        }
        return is_array($values) ? $result : current($result);
    }
}
