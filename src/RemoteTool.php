<?php

namespace Laravel\Remote2Model;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class RemoteTool
{
    public function queryToCondition($query)
    {
        if ($query instanceof EloquentBuilder) {
            $query = $query->getQuery();
        }

        $array = [];

        $selectComponents = ['aggregate', 'columns', 'from', 'joins', 'wheres', 'groups', 'havings', 'orders', 'limit', 'offset', 'lock', 'bindings', 'distinct', 'unions'];

        foreach ($selectComponents as $component) {
            if (isset($query->$component) && !is_null($query->$component)) {
                $value = $query->$component;

                if (in_array($component, ['columns', 'groups'])) {
                    foreach ($value as $key => $val) {
                        if (is_object($val)) {
                            $val = [
                                'class' => get_class($val),
                                'value' => strval($val),
                            ];
                        }
                        $value[$key] = $val;
                    }
                }

                if ($component == 'wheres') {
                    foreach ($value as $key => $val) {
                        if (in_array($val['type'], ['Nested', 'Exists'])) {
                            $val['query'] = self::queryToCondition($val['query']);
                        }
                        $value[$key] = $val;
                    }
                }

                if ($component == 'joins') {
                    foreach ($value as $key => $val) {
                        $table = $val->table;
                        if (is_object($table)) {
                            $table = [
                                'class' => get_class($table),
                                'value' => strval($table),
                            ];
                        }

                        $joinArr = [
                            'type' => $val->type,
                            'table' => $table,
                            'joinQuery' => self::queryToCondition($val),
                        ];
                        $value[$key] = $joinArr;
                    }
                }

                if ($component == 'unions') {
                    foreach ($value as $key => $val) {
                        $val['query'] = self::queryToCondition($val['query']);
                        $value[$key] = $val;
                    }
                }
                $array[$component] = $value;
            }
        }

        $env = app()->environment();
        if (in_array($env, ['local', 'dev', 'development', 'testing'])) {
            $json = json_encode($array, JSON_UNESCAPED_UNICODE);
            return json_decode($json, true);
        } else {
            return $array;
        }

    }

    public function conditionToQuery($condition, $newQuery = null)
    {
        $newQuery = $newQuery ?? DB::query();
        foreach ($condition as $component => $value) {
            if (in_array($component, ['columns', 'groups'])) {
                foreach ($value as $key => $val) {
                    if (is_array($val)) {
                        $class = $val['class'];
                        $val = new $class($val['value']);
                    }
                    $value[$key] = $val;
                }
            }

            if ($component == 'wheres') {
                foreach ($value as $key => $val) {
                    if (in_array($val['type'], ['Nested', 'Exists'])) {
                        $val['query'] = self::conditionToQuery($val['query']);
                    }
                    $value[$key] = $val;
                }
            }

            if ($component == 'joins') {
                foreach ($value as $key => $val) {
                    $type = $val['type'];
                    $table = $val['table'];
                    if (is_array($table)) {
                        $class = $table['class'];
                        $table = new $class($table['value']);
                    }
                    $joinQuery = new JoinClause($newQuery, $type, $table);
                    $joinQuery = self::conditionToQuery($val['joinQuery'], $joinQuery);
                    $value[$key] = $joinQuery;
                }
            }

            if ($component == 'unions') {
                foreach ($value as $key => $val) {
                    $val['query'] = self::conditionToQuery($val['query']);
                    $value[$key] = $val;
                }
            }

            $newQuery->$component = $value;
        }

        return $newQuery;
    }
}
