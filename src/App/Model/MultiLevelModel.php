<?php

namespace Wengg\WebmanLaravelBase\App\Model;

use Wengg\WebmanLaravelBase\App\Triats\MultiLevelModelTrait;

/**
 * 多层级基类模型
 * @author mosquito <zwj1206_hi@163.com>
 */
abstract class MultiLevelModel extends BModel
{
    use MultiLevelModelTrait;
}
