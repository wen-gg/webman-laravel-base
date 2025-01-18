<?php

namespace WenGg\WebmanLaravelBase\Triats;

// //示例
// enum State: int
// {
//     use EnumTriat;

//     /**
//      * 开启
//      */
//     case ON = 1;
//     /**
//      * 关闭
//      */
//     case OFF = 0;
// }

trait EnumTriat
{
    use ConfigTrait;

    /**
     * 获取所有值
     * @author mosquito <zwj1206_hi@163.com>
     */
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

    /**
     * 值转对象
     * @author mosquito <zwj1206_hi@163.com>
     */
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

    /**
     * 获取描述
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getDescConf($key = null, string $keyName = null, string $valueName = null)
    {
        $refc = new \ReflectionEnum(static::class);
        $cases = $refc->getCases();
        $arr = [];
        foreach ($cases as $val) {
            $arr[$val->getValue()->value] = preg_replace('/\/\*\*|\*\/|\*\s|\s+/', '', $val->getDocComment() ?: '') ?: $val->getName();
        }
        return static::configFormatNew($arr, $key, $keyName, $valueName);
    }
}
