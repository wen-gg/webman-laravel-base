<?php

namespace WenGg\WebmanLaravelBase\Models;

use WenGg\WebmanLaravelBase\Triats\MultiLevelXsModelTrait;

/**
 * 多层级基类模型
 * @author mosquito <zwj1206_hi@163.com>
 */
abstract class MultiLevelXsModel extends BModel
{
    use MultiLevelXsModelTrait;
}
