<?php

namespace Tests\Unit;

use App\Models\OrderDetailModel;
use App\RemoteModels\OrderDetailsRemote;
use App\RemoteModels\ProductRemote;
use Tests\TestCase;
use \Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RemoteModelTest extends TestCase
{
    public function testExists()
    {
        $ret = OrderDetailsRemote::where('o_id', 1)->exists();
        var_dump($ret->toJson());
    }

    public function testPaginate()
    {
        $ret = OrderDetailsRemote::paginate();
        var_dump($ret->toJson());
    }

    public function testJoin()
    {
        $detail = OrderDetailsRemote::join('order', 'order_detail.o_id', '=', 'order.o_id')->where('o_id',1)->offset(1)->limit(2)->orderBy('o_id','desc')->get();
        var_dump($detail->toJson());
    }
    public function testCount()
    {
        $count = OrderDetailsRemote::where('o_id', 1)->count();
        // $count = OrderDetailModel::where('o_id', 1)->count();
        var_dump($count);
    }

    public function testRange()
    {
        $ret = OrderDetailsRemote::where('o_id', '<>', 1)->get();
        var_dump($ret->toJson());
    }

    public function testClosure()
    {
        $ret = OrderDetailsRemote::where(function($query){
            $query->where('o_id', 1)->where(function($query){
                $query->where('product_id', 1);
            });
        })->first();
        var_dump($ret->toJson());
    }
    public function testFind()
    {
        $ret = OrderDetailsRemote::findOrFail(2);
        var_dump($ret->toJson());
    }

    public function testDelete()
    {
        $ret = OrderDetailsRemote::destroy(1);
        var_dump($ret);
    }

    public function testCreate()
    {
        $item = OrderDetailsRemote::create([
            'o_id' => 1,
            'product_id' => 101,
        ]);

        // $item = new OrderDetailsRemote();
        // $item->o_id = 1;
        // $item->product_id = 120;
        // $item->save();

        // $item = OrderDetailsRemote::updateOrCreate(
        //     ['product_id'=>101],
        //     ['o_id'=>1]
        // );

        var_dump($item->toJson());
    }
    public function testUpdate()
    {
        $affact = OrderDetailsRemote::save(['o_id' => 1, 'status' => 1]);
        var_dump($affact);
    }

    public function testValue()
    {
        $status = OrderDetailsRemote::where('o_id', 1)->value('status');
        var_dump($status);
    }

    public function testPluck()
    {
        $ret = OrderDetailsRemote::where('o_id', 1)->pluck('status');
        var_dump($ret->toJson());
    }

    public function testFirst()
    {
        // $order = OrderModel::with(['orderDetailsRemote'])->where('o_id', 1)->select(['o_id','wo_number'])->get();
        // var_dump($order->toJson());

        // $detail = OrderDetailsRemote::find([1, 2]);
        // $detail = OrderDetailsRemote::where('o_id',1)->offset(1)->limit(2)->orderBy('o_id','desc')->get();
        // $detail = OrderDetailModel::where('od_id', 1)->select(['od_id','o_id', 'product_id'])->first();
        
        $detail = OrderDetailsRemote::where('od_id', 1)->select(['od_id','o_id', 'product_id'])->first();
        var_dump($detail->toJson());
    }

    public function testUnion()
    {
        $first = OrderDetailModel::where('o_id', 1);
        $ret = OrderDetailModel::where('o_id', 2)->union($first)->get();
        var_dump($ret->toJson());
    }

    public function testDate()
    {
        $ret = ProductModel::whereDate('delete_time', '2019-01-01')->get();
        var_dump($ret->toJson());
    }

    public function testGet()
    {
        $ret = ProductModel::get();
        var_dump($ret->toJson());
    }

    public function testSubSelect()
    {
        $ret = OrderDetailModel::where(function($query){
            $query->select('id')->from('product')->whereColumn('id','order_detail.product_id')->limit(1);
        }, 'Pro')->get();
        var_dump($ret->toJson());
    }

    public function testIncrement()
    {
        // $ret = ProductRemote::where('id', 1)->increment('status', 1);
        $ret = ProductRemote::where('id', 1)->first();

        var_dump($ret);
    }

    public function testCondition()
    {
        // $query = OrderDetailsRemote::where('o_id',1)->select('product_id')->distinct()->offset(1)->limit(2)->orderBy('o_id','desc')->getQuery();

        // $query = OrderDetailsRemote::where('o_id',1)->select('product_id')->distinct()->getQuery();

        // $query = OrderDetailsRemote::where('o_id', 1)->select(DB::raw('count(product_id) as product_num'))->getQuery();

        $query = OrderDetailsRemote::join('order', 'order_detail.o_id', '=', 'order.o_id')->where(function($query){
            $query->where('order.o_id',1);
        })->select([DB::raw('count(order_detail.product_id) as product_num'), 'order.o_id'])->getQuery();

        // $sql = $query->toSql();
        // var_dump($sql);

        $one = 1;

        // $query = OrderDetailsRemote::where('order.o_id',1)->join('order', function($join) {
        //     $join->on('order_detail.o_id', '=', 'order.o_id');
        // })->getQuery();

        // $query = OrderDetailsRemote::whereIn('o_id', function($query) {
        //     $query->select('o_id')->from('order');
        // })->getQuery();

        // $query = OrderDetailsRemote::where(function($query) {
        //     $query->where('o_id', 1);
        // })->getQuery();

        // $query = OrderDetailsRemote::groupByRaw('o_id')->havingRaw('count(product_id) > 1')->getQuery();

        // $query = OrderDetailsRemote::leftJoin('order', 'order.o_id', '=', 'order_detail.o_id')->join('product', 'product.id', '=', 'order_detail.product_id')->getQuery();

        // $query = OrderDetailsRemote::where('order.o_id',1)->join('order', function($join) {
        //     $join->on('order_detail.o_id', '=', 'order.o_id')->where('order_detail.o_id', 1);
        // })->getQuery();

        // $tableQuery = OrderModel::where('o_id', 1);
        // $query = OrderDetailsRemote::joinSub($tableQuery, 'a', function($join){
        //     $join->on('a.o_id', '=', 'order_detail.o_id');
        // })->getQuery();

        // $first = OrderDetailsRemote::where('o_id', 1);
        // $query = OrderDetailsRemote::where('o_id', 2)->union($first)->getQuery();

        // $query = OrderDetailsRemote::whereExists(function($query){
        //     $query->select(DB::raw(1))
        //     ->from('product')
        //     ->whereRaw('order_detail.product_id = product.id');
        // })->getQuery();

        // $query = OrderDetailsRemote::whereHas('productRemote')->getQuery();

        // $query = OrderDetailModel::where(function($query){
        //     $query->select('id')->from('product')->whereColumn('id','order_detail.product_id')->limit(1);
        // }, 'Pro')->getQuery();

        $condition = $this->queryToCondition($query);
        $newQuery = DB::query();
        $newQuery = $this->conditionToQuery($newQuery, $condition);
        $item = $newQuery->get();
        var_dump('', (new Collection($item))->toJson());
    }

    public function testConditionUpdate()
    {
        // $affact = OrderDetailsRemote::where('od_id', 1)->update(['status' => 'demo']);
        // $item = OrderDetailsRemote::where('od_id', 1)->first();
        $affact = OrderDetailModel::where('od_id', 1)->update(['status' => 'demo']);
        $item = OrderDetailModel::where('od_id', 1)->first();
        var_dump($affact, $item->toJson());
    }

    public function queryToCondition(Builder $query)
    {
        $array = [];

        $selectComponents = [ 'aggregate', 'columns', 'from', 'joins', 'wheres', 'groups', 'havings', 'orders', 'limit', 'offset', 'lock', 'bindings', 'distinct', 'unions'];

        foreach ($selectComponents as $component) {
            if (isset($query->$component) && ! is_null($query->$component)) {
                $value = $query->$component;

                if ($component == 'bindings'){
                    $value = array_filter($value);
                }

                if (in_array($component, ['columns', 'groups'])) {
                    foreach($value as $key => $val){
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
                    foreach($value as $key => $val) {
                        if (in_array($val['type'], ['Nested', 'Exists'])) {
                            $val['query'] = $this->queryToCondition($val['query']);
                        }
                        $value[$key] = $val;
                    }
                }

                if ($component == 'joins') {
                    foreach($value as $key => $val) {
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
                            'joinQuery' => $this->queryToCondition($val),
                        ];
                        $value[$key] = $joinArr;
                    }
                }

                if ($component == 'unions') {
                    foreach($value as $key => $val) {
                        $val['query'] = $this->queryToCondition($val['query']);
                        $value[$key] = $val;
                    }
                }
                $array[$component] = $value;
            }
        }
        
        // $json = json_encode($array, JSON_UNESCAPED_UNICODE);
        // return json_decode($json, true);

        return $array;
        // $json = serialize($array);
        // return unserialize($json);
    }

    public function conditionToQuery($newQuery, $condition)
    {
        foreach($condition as $component => $value)
        {
            if (in_array($component, ['columns', 'groups'])) {
                foreach($value as $key => $val) {
                    if (is_array($val)) {
                        $class = $val['class'];
                        $val = new $class($val['value']);
                    }
                    $value[$key] = $val;
                }
            }

            if ($component == 'wheres') {
                foreach($value as $key => $val) {
                    if (in_array($val['type'], ['Nested', 'Exists'])) {
                        $query = DB::query();
                        $val['query'] = $this->conditionToQuery($query, $val['query']);
                    }
                    $value[$key] = $val;
                }
            }

            if ($component == 'joins') {
                foreach($value as $key => $val) {
                    $type = $val['type'];
                    $table = $val['table'];
                    if (is_array($table)) {
                        $class = $table['class'];
                        $table = new $class($table['value']);
                    }
                    $joinQuery = new JoinClause($newQuery, $type, $table);
                    $joinQuery = $this->conditionToQuery($joinQuery, $val['joinQuery']);
                    $value[$key] = $joinQuery;
                }
            }

            if ($component == 'unions') {
                foreach($value as $key => $val) {
                    $query = DB::query();
                    $val['query'] = $this->conditionToQuery($query, $val['query']);
                    $value[$key] = $val;
                }
            }

            $newQuery->$component = $value;
        }

        return $newQuery;
    }
    
}
