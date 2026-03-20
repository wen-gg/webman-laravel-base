<?php

if (!function_exists('validator')) {
    /**
     * Create a new Validator instance.
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Contracts\Validation\Factory
     */
    function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $factory = new \WenGg\WebmanLaravelBase\Factories\ValidatorFactory();
        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->make($data, $rules, $messages, $customAttributes);
    }
}

if (!function_exists('laravel_batch_update')) {
    /**
     * laravel数据库单表批量更新，适用于laravel
     * @author mosquito <zwj1206_hi@163.com>
     */
    function laravel_batch_update(string $table, array $list_data, int $chunk_size = 200, string $connection = null)
    {
        if (count($list_data) < 1) {
            throw new \Exception('更新数量不能小于1');
        }
        if ($chunk_size < 1) {
            throw new \Exception('分切数量不能小于1');
        }
        $chunk_list = array_chunk($list_data, $chunk_size);
        $count = 0;
        foreach ($chunk_list as $list_item) {
            $first_row = current($list_item);
            $update_col = array_keys($first_row);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $reference_col = isset($first_row['id']) ? 'id' : current($update_col);
            unset($update_col[0]);
            // 拼接sql语句
            $update_sql = 'UPDATE ' . $table . ' SET ';
            $sets = [];
            $bindings = [];
            foreach ($update_col as $u_col) {
                $set_sql = '`' . $u_col . '` = CASE ';
                foreach ($list_item as $item) {
                    $set_sql .= 'WHEN `' . $reference_col . '` = ? THEN ';
                    $bindings[] = $item[$reference_col];
                    if ($item[$u_col] instanceof \Illuminate\Database\Query\Expression) {
                        $set_sql .= $item[$u_col]->getValue() . ' ';
                    } else {
                        $set_sql .= '? ';
                        $bindings[] = $item[$u_col];
                    }
                }
                $set_sql .= 'ELSE `' . $u_col . '` END ';
                $sets[] = $set_sql;
            }
            $update_sql .= implode(', ', $sets);
            $where_in = collect($list_item)->pluck($reference_col)->values()->all();
            $bindings = array_merge($bindings, $where_in);
            $where_in = rtrim(str_repeat('?,', count($where_in)), ',');
            $update_sql = rtrim($update_sql, ', ') . ' WHERE `' . $reference_col . '` IN (' . $where_in . ')';
            //
            $count += \support\Db::connection($connection)->update($update_sql, $bindings);
        }
        return $count;
    }
}

if (!function_exists('laravel_paginate')) {
    /**
     * laravel分页查询兼容group，如果total不为null则使用虚拟查询即不查询总数，适用于laravel
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $builder
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author mosquito <zwj1206_hi@163.com>
     */
    function laravel_paginate($builder, int $perPage = 15, array $columns = ['*'], string $pageName = 'page', int $page = null, int $total = null)
    {
        if (!is_null($total)) {
            $page = $page ?: \Illuminate\Pagination\Paginator::resolveCurrentPage($pageName);
            $results = $builder->forPage($page, $perPage)->get($columns);
        } else {
            if (!isset($builder->groups) || !$builder->groups) {
                return $builder->paginate($perPage, $columns, $pageName, $page);
            }
            $page = $page ?: \Illuminate\Pagination\Paginator::resolveCurrentPage($pageName);
            $c_builder = clone $builder;
            if (!$c_builder->columns) {
                $c_builder->select($columns);
            }
            $sql = $c_builder->cloneWithout(['orders', 'limit', 'offset'])
                ->cloneWithoutBindings(['select', 'order'])->toSql();
            $total = \support\Db::connection($c_builder->getConnection()->getName())
                ->select('select count(1) as counts from (' . $sql . ') as temp', $c_builder->getBindings())[0]->counts;
            $results = $total > 0 ? $builder->forPage($page, $perPage)->get($columns) : collect();
        }
        return new \Illuminate\Pagination\LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
}

if (!function_exists('laravel_paginate_format')) {
    /**
     * laravel分页格式化
     * @param \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator $pagination
     * @author mosquito <zwj1206_hi@163.com>
     */
    function laravel_paginate_format($pagination)
    {
        return [
            'per_page' => $pagination->perPage(),
            'current_page' => $pagination->currentPage(),
            'last_page' => $pagination->lastPage(),
            'total' => $pagination->total(),
            'data' => $pagination->values()->toArray(),
        ];
    }
}
