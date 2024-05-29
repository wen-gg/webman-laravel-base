<?php

namespace WenGg\WebmanLaravelBase\Models;

use WenGg\WebmanLaravelBase\Triats\BModelTrait;
use support\Model;

/**
 * 基类模型
 * @author mosquito <zwj1206_hi@163.com>
 */
abstract class BModel extends Model
{
    use BModelTrait;
}
