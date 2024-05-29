<?php

namespace WenGg\WebmanLaravelBase\Triats;

use WenGg\WebmanLaravelBase\Casts\Arr;
use support\Db as DB;

/**
 * 多层级模型复用，适用于laravel
 * @mixin \WenGg\WebmanLaravelBase\Models\BModel
 * @author mosquito <zwj1206_hi@163.com>
 */
trait MultiLevelModelTrait
{
    /**
     * 索引对照
     * @author mosquito <zwj1206_hi@163.com>
     */
    protected static $_multiLevelMap = [
        'id' => 'id',
        'pid' => 'pid',
        'pid_path' => 'pid_path', //为null表示无该字段
    ];

    /**
     * 设置列信息
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function setMapColumn(string $id, string $pid, string $pid_path = null)
    {
        static::$_multiLevelMap = [
            'id' => $id,
            'pid' => $pid,
            'pid_path' => $pid_path,
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
     * 获取pid_path列
     * @return string
     * @author mosquito <zwj1206_hi@163.com>
     */
    public static function getPidPathColumn()
    {
        return static::$_multiLevelMap['pid_path'];
    }

    /**
     * 父级关联
     * @author mosquito <zwj1206_hi@163.com>
     */
    public function parent()
    {
        return $this->belongsTo(static::class, static::getPidColumn(), static::getIdColumn());
    }

    /**
     * 子级关联
     * @author mosquito <zwj1206_hi@163.com>
     */
    public function children()
    {
        return $this->hasMany(static::class, static::getPidColumn(), static::getIdColumn());
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
            $item = static::where(static::getIdColumn(), $item)->first();
        }
        if (!$item) {
            return collect();
        }

        //
        $parents = collect();
        if (is_null(static::getPidPathColumn())) {
            $parent_func = function ($item) use (&$parent_func, &$parents) {
                $parent = $item->parent;
                $item->unsetRelation('parent');
                if ($parent && $parent->{static::getIdColumn()}) {
                    $parents->prepend($parent);
                    $parent_func($parent);
                }
            };
            $parent_func($item);
        } else {
            $pid_path_array = false;
            if ($item->hasCast(static::getPidPathColumn(), strtolower(Arr::class))) {
                $pid_path_array = true;
            }
            $path_arr = $pid_path_array ? $item->{static::getPidPathColumn()} : (json_decode($item->{static::getPidPathColumn()}, true) ?: []);
            if ($path_arr) {
                $parents = static::whereIn(static::getIdColumn(), $path_arr)
                    ->orderByRaw("field(" . static::getIdColumn() . "," . implode(',', $path_arr) . ")")
                    ->get();
            }
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
            $item = static::where(static::getIdColumn(), $item)->first();
        }
        if (!$item) {
            return collect();
        }

        //
        $children = collect();
        if (is_null(static::getPidPathColumn())) {
            $child_func = function ($item) use (&$child_func, &$children) {
                $childList = $item->children;
                $item->unsetRelation('children');
                foreach ($childList as $child) {
                    $children->push($child);
                }
                foreach ($childList as $child) {
                    $child_func($child);
                }
            };
            $child_func($item);
        } else {
            $children = static::whereJsonContains(static::getPidPathColumn(), $item->{static::getIdColumn()})
                ->orderByRaw("json_length(" . static::getPidPathColumn() . ")")
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
            $item = static::where(static::getIdColumn(), $item)->first();
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
        if (!is_null(static::getPidPathColumn())) {
            $pid_path_array = false;
            if ($item->hasCast(static::getPidPathColumn(), strtolower(Arr::class))) {
                $pid_path_array = true;
            }
            $old_path_arr = $pid_path_array ? $item->{static::getPidPathColumn()} : (json_decode($item->{static::getPidPathColumn()}, true) ?: []);
            if ($pid == 0) {
                $item->{static::getPidPathColumn()} = null;
            } else {
                $item->{static::getPidPathColumn()} = $pid_path_array ? $parents_ids : json_encode($parents_ids);
            }
        }
        if ($item->isDirty()) {
            $temp = $item->save();
            if ($temp === false) {
                throw new \Exception('更新当前项失败');
            }

            //更新当前项子级
            if (!is_null(static::getPidPathColumn())) {
                $new_path_arr = $pid_path_array ? $item->{static::getPidPathColumn()} : (json_decode($item->{static::getPidPathColumn()}, true) ?: []);
                if ($old_path_arr != $new_path_arr) {
                    $new_path_sql = static::getPidPathColumn();
                    if ($old_path_arr) {
                        $json_remove_arr = array_pad([], count($old_path_arr), '"$[0]"');
                        $new_path_sql = 'JSON_REMOVE(`' . static::getPidPathColumn() . '`, ' . implode(',', $json_remove_arr) . ')';
                    }
                    if ($new_path_arr) {
                        $new_path_arr = array_reverse($new_path_arr);
                        $new_path_sql = 'JSON_ARRAY_INSERT(' . $new_path_sql;
                        foreach ($new_path_arr as $cpath) {
                            $new_path_sql .= ', "$[0]", ' . $cpath;
                        }
                        $new_path_sql .= ')';
                    }
                    //
                    $temp = static::whereJsonContains(static::getPidPathColumn(), $item->{static::getIdColumn()})
                        ->update([
                            static::getPidPathColumn() => DB::raw($new_path_sql),
                        ]);
                    if ($temp === false) {
                        throw new \Exception('更新当前项子级失败');
                    }
                }
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
