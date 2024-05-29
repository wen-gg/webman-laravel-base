<?php

namespace WenGg\WebmanLaravelBase\Triats;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 基类模型复用，适用于laravel
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @author mosquito <zwj1206_hi@163.com>
 */
trait BModelTrait
{
    use HasFactory, ConfigTrait;

    // protected static $unguarded = true;

    /**
     * @inheritDoc
     * @author mosquito <zwj1206_hi@163.com>
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(class_basename($this));
    }

    /**
     * @inheritDoc
     * @param \DateTimeInterface $date
     * @author mosquito <zwj1206_hi@163.com>
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }

    /**
     * 获取模型表名
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getTableName()
    {
        return (new static)->getTable();
    }

    /**
     * 获取模型表前缀
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getTablePrefix()
    {
        return (new static)->getConnection()->getTablePrefix();
    }

    /**
     * 获取模型表全名
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getFullTableName()
    {
        return static::getTablePrefix() . static::getTableName();
    }

    /**
     * 获取模型所有列
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getColumns()
    {
        $model = new static;
        //enum注册
        $model->getConnection()->getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        return array_keys($model->getConnection()->getDoctrineSchemaManager()->listTableColumns($model->getConnection()->getTablePrefix() . $model->getTable()));
    }

    /**
     * 获取模型列的交集
     * @param array $columns
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function intersectColumns(array $columns)
    {
        $has_columns = static::getColumns();
        $arr = array_combine($has_columns, array_fill(0, count($has_columns), ''));
        return array_intersect_key($columns, $arr);
    }
}
