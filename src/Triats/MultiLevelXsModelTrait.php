<?php

namespace WenGg\WebmanLaravelBase\Triats;

/**
 * 多层级模型复用（细粒度型），适用于laravel
 * @mixin \WenGg\WebmanLaravelBase\Models\BModel
 * @author mosquito <zwj1206_hi@163.com>
 */
trait MultiLevelXsModelTrait
{
    /**
     * 索引对照
     * @author mosquito <zwj1206_hi@163.com>
     */
    protected static $_multiLevelMap = [
        'id' => 'id',
        'pid' => 'pid',
        'level' => 'level',
    ];
    protected static $_multiLevelMin = 1;

    /**
     * 设置列信息
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function setMapColumn(string $id, string $pid, string $level)
    {
        static::$_multiLevelMap = [
            'id' => $id,
            'pid' => $pid,
            'level' => $level,
        ];
    }

    /**
     * 获取id列
     * @return string
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getIdColumn()
    {
        return static::$_multiLevelMap['id'];
    }

    /**
     * 获取pid列
     * @return string
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getPidColumn()
    {
        return static::$_multiLevelMap['pid'];
    }

    /**
     * 获取level列
     * @return string
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getLevelColumn()
    {
        return static::$_multiLevelMap['level'];
    }

    /**
     * 父级关联
     * @author mosquito <zwj1206_hi@163.com>
     */
    public function parent()
    {
        return $this->belongsTo(static::class, static::getPidColumn(), static::getIdColumn())->where(static::getLevelColumn(), static::$_multiLevelMin);
    }

    /**
     * 子级关联
     * @author mosquito <zwj1206_hi@163.com>
     */
    public function children()
    {
        return $this->hasMany(static::class, static::getPidColumn(), static::getIdColumn())->where(static::getLevelColumn(), static::$_multiLevelMin);
    }

    /**
     * 获取全部父级列表
     * @param self|int $item
     * @param bool $self
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getAllParentList($item, bool $self = true)
    {
        if ($item instanceof static) {
            //
        } else {
            $item = intval($item);
            if ($item <= 0) {
                return collect();
            }
            $item = static::where(static::getIdColumn(), $item)->where(static::getLevelColumn(), static::$_multiLevelMin)->first();
        }
        if (!$item) {
            return collect();
        }

        //
        $parents = collect();
        $path_arr = static::where(static::getIdColumn(), $item->{static::getIdColumn()})->where(static::getPidColumn(), '>', 0)->orderBy(static::getLevelColumn(), 'desc')->pluck(static::getPidColumn())->toArray();
        if ($path_arr) {
            $parents = static::whereIn(static::getIdColumn(), $path_arr)
                ->where(static::getLevelColumn(), static::$_multiLevelMin)
                ->orderByRaw("field(" . static::getIdColumn() . "," . implode(',', $path_arr) . ")")
                ->get();
        }
        return $self ? $parents->push($item) : $parents;
    }

    /**
     * 获取全部子级列表
     * @param self|int $item
     * @param bool $self
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getAllChildrenList($item, bool $self = true)
    {
        if ($item instanceof static) {
            //
        } else {
            $item = intval($item);
            if ($item <= 0) {
                return collect();
            }
            $item = static::where(static::getIdColumn(), $item)->where(static::getLevelColumn(), static::$_multiLevelMin)->first();
        }
        if (!$item) {
            return collect();
        }

        //
        $children = collect();
        $path_arr = static::where(static::getPidColumn(), $item->{static::getIdColumn()})->orderBy(static::getLevelColumn())->pluck(static::getIdColumn())->toArray();
        if ($path_arr) {
            $children = static::whereIn(static::getIdColumn(), $path_arr)
                ->where(static::getLevelColumn(), static::$_multiLevelMin)
                ->orderByRaw("field(" . static::getIdColumn() . "," . implode(',', $path_arr) . ")")
                ->get();
        }
        return $self ? $children->prepend($item) : $children;
    }

    /**
     * 更新当前项的父级信息
     * @param self|int $item
     * @param self|int $parent
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function updateItemParent($item, $parent = null)
    {
        if ($item instanceof static) {
            //
        } else {
            $item = intval($item);
            if ($item <= 0) {
                throw new \Exception("获取当前项失败");
            }
            $item = static::where(static::getIdColumn(), $item)->where(static::getLevelColumn(), static::$_multiLevelMin)->first();
        }
        if (!$item) {
            throw new \Exception('获取当前项失败');
        }

        //
        $pid = 0;
        if ($parent instanceof static) {
            $pid = $parent->{static::getIdColumn()};
        } elseif (is_null($parent)) {
            $pid = $item->{static::getPidColumn()};
        } elseif (is_numeric($parent)) {
            $pid = intval($parent);
            $parent = null;
        } else {
            throw new \Exception('父级信息错误');
        }
        if ($item->{static::getPidColumn()} == $pid) {
            return;
        }
        $parent = null;
        if ($pid > 0) {
            $parent = static::where(static::getIdColumn(), $pid)->first();
            if (!$parent) {
                throw new \Exception("父级信息错误");
            }
        }

        //
        $parents_ids = static::getAllParentList($parent)->pluck(static::getIdColumn())->toArray();
        if (in_array($item->{static::getIdColumn()}, $parents_ids)) {
            throw new \Exception('父级信息不能是当前项的子级');
        }

        //更新当前项
        $item->{static::getPidColumn()} = $pid;
        if ($item->isDirty()) {
            $temp = $item->save();
            if ($temp === false) {
                throw new \Exception('更新当前项失败');
            }

            //
            $parents_arr = [];
            $branch_func = function (self $item) use (&$parents_arr) {
                //
                static::where(static::getIdColumn(), $item->{static::getIdColumn()})->where(static::getLevelColumn(), '>', static::$_multiLevelMin)->delete();
                if (!isset($parents_arr[$item->{static::getPidColumn()}])) {
                    $parents = $item->{static::getPidColumn()} > 0 ? static::getAllParentList($item->parent, false)->reverse() : collect();
                    $parents_arr[$item->{static::getPidColumn()}] = $parents;
                } else {
                    $parents = $parents_arr[$item->{static::getPidColumn()}];
                }
                $item_arr = [];
                $item->unsetRelation('parent');
                $old_val = $item->toArray();
                foreach ($parents as $value) {
                    $val = array_merge($old_val, [
                        static::getPidColumn() => $value->{static::getIdColumn()},
                        static::getLevelColumn() => $item->{static::getLevelColumn()} + 1 + count($item_arr),
                    ]);
                    $item_arr[] = $val;
                }
                $val = array_merge($old_val, [
                    static::getPidColumn() => 0,
                    static::getLevelColumn() => $item->{static::getLevelColumn()} + 1 + count($item_arr),
                ]);
                $item_arr[] = $val;
                if ($item_arr) {
                    static::insert($item_arr);
                }
            };
            $branch_func($item);

            //更新当前项子级
            $children = static::getAllChildrenList($item, false);
            foreach ($children as $child) {
                $branch_func($child);
            }
        }
    }

    /**
     * 获取指定项指定字段的全名称
     * @param self|int $item
     * @param bool $self
     * @param string $glue
     * @param string $field
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getItemFullField($item, bool $self = true, string $glue = '/', string $field = null)
    {
        return implode($glue, array_column(static::getAllParentList($item, $self)->toArray(), is_null($field) ? static::getIdColumn() : $field));
    }

    /**
     * 获取缺少关系的ID组
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getLackIds()
    {
        return static::whereNotIn(static::getPidColumn(), function ($query) {
            $query->from(static::getTableName())->distinct()->select(static::getIdColumn());
        })
            ->where(static::getPidColumn(), '>', 0)
            ->distinct()
            ->pluck(static::getPidColumn())
            ->toArray();
    }
}
