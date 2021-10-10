<?php

namespace Tests\Unit;

use App\Models\OrderRemote;
use App\Models\OrderDetailRemote;
use App\Models\ProductRemote;
use Tests\TestCase;
use \Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RemoteModelTest extends TestCase
{
    public function testWith()
    {
        $order = OrderRemote::with([
            'orderDetails.product'
        ])->first();
        
        $product = $order->orderDetails[0]->product;
        $table = $product->getTable();
        $item = DB::table($table)->where('pid', $product->pid)->first();

        $this->assertEquals($product->toJson(JSON_NUMERIC_CHECK), json_encode($item, JSON_NUMERIC_CHECK));
        
    }
    public function testFind()
    {
        $remote = OrderDetailRemote::findOrFail(2);
        $table = $remote->getTable();
        $item = DB::table($table)->where('od_id', 2)->first();

        $this->assertEquals($remote->toJson(JSON_NUMERIC_CHECK), json_encode($item, JSON_NUMERIC_CHECK));
    }

    public function testFirst()
    {
        $remote = OrderDetailRemote::where('od_id', 2)->select([DB::raw('od_id'), 'o_id', 'product_id'])->first();
        $table = $remote->getTable();
        $item = DB::table($table)->where('od_id', 2)->select([DB::raw('od_id'), 'o_id', 'product_id'])->first();

        $this->assertEquals($remote->toJson(JSON_NUMERIC_CHECK), json_encode($item, JSON_NUMERIC_CHECK));
    }

    public function testValue()
    {
        $remoteStatus = OrderDetailRemote::where('od_id', 2)->value('status');
        $table = (new OrderDetailRemote)->getTable();
        $status = DB::table($table)->where('od_id', 2)->value('status');

        $this->assertEquals($remoteStatus, $status);
    }

    public function testGet()
    {
        $remoteList = ProductRemote::where('store_id', 1)->get();
        $table = (new ProductRemote)->getTable();
        $list = DB::table($table)->where('store_id', 1)->get();

        $this->assertEquals($remoteList->toJson(JSON_NUMERIC_CHECK), json_encode($list, JSON_NUMERIC_CHECK));
    }

    public function testPluck()
    {
        $remote = OrderDetailRemote::where('o_id', 1)->pluck('od_id');
        $table = (new OrderDetailRemote)->getTable();
        $item = DB::table($table)->where('o_id', 1)->pluck('od_id');

        $this->assertEquals($remote->toJson(JSON_NUMERIC_CHECK), json_encode($item, JSON_NUMERIC_CHECK));
    }

    public function testRange()
    {
        $remote = OrderDetailRemote::where('o_id', '<>', 1)->get();
        $table = (new OrderDetailRemote)->getTable();
        $item = DB::table($table)->where('o_id', '<>', 1)->get();

        $this->assertEquals($remote->toJson(JSON_NUMERIC_CHECK), json_encode($item, JSON_NUMERIC_CHECK));
    }

    public function testClosure()
    {
        $remote = OrderDetailRemote::where(function($query){
            $query->where('o_id', 1)->where(function($query){
                $query->where('product_id', 1);
            });
        })->first();
        $table = (new OrderDetailRemote)->getTable();
        $item = DB::table($table)->where('o_id', 1)->where('product_id', 1)->first();

        $this->assertEquals($remote->toJson(JSON_NUMERIC_CHECK), json_encode($item, JSON_NUMERIC_CHECK));
    }

    public function testExists()
    {
        $remoteExists = OrderDetailRemote::where('od_id', 1)->exists();
        $table = (new OrderDetailRemote)->getTable();
        $itemExists = DB::table($table)->where('od_id', 1)->exists();

        $this->assertEquals($remoteExists, $itemExists);
    }

    public function testCount()
    {
        $count = OrderDetailRemote::where('o_id', 1)->count();
        $table = (new OrderDetailRemote)->getTable();
        $total = DB::table($table)->where('o_id', 1)->count();
        $this->assertEquals($count, $total);
    }

    public function testPaginate()
    {
        $remote = OrderDetailRemote::paginate();
        $paginateArr = $remote->toArray();
        $table = (new OrderDetailRemote)->getTable();
        $total = DB::table($table)->count();
        $list = DB::table($table)->limit(15)->get();
        
        $this->assertEquals($paginateArr['total'], $total);
        $this->assertEquals(json_encode($paginateArr['data'], JSON_NUMERIC_CHECK), json_encode($list, JSON_NUMERIC_CHECK));

    }

    public function testJoin()
    {
        $remoteList = OrderDetailRemote::join('order', 'order_detail.o_id', '=', 'order.o_id')->where('order.o_id',1)->orderBy('o_id','desc')->get();
        
        $table = (new OrderDetailRemote)->getTable();
        $list = DB::table($table)->join('order', 'order_detail.o_id', '=', 'order.o_id')->where('order.o_id',1)->orderBy('o_id','desc')->get();

        $this->assertEquals($remoteList->toJson(JSON_NUMERIC_CHECK), json_encode($list, JSON_NUMERIC_CHECK));

    }

    public function testUnion()
    {
        $first = OrderDetailRemote::where('o_id', 1);
        $remoteList = OrderDetailRemote::where('o_id', 2)->union($first)->get();
        
        $table = (new OrderDetailRemote)->getTable();
        $second =DB::table($table)->where('o_id', 1);
        $list = DB::table($table)->where('o_id', 2)->union($second)->get();

        $this->assertEquals($remoteList->toJson(JSON_NUMERIC_CHECK), json_encode($list, JSON_NUMERIC_CHECK));
    }

    public function testSubSelect()
    {
        $remoteList = OrderDetailRemote::where(function($query){
            $query->select('id')->from('product')->whereColumn('id','order_detail.product_id')->limit(1);
        }, 'Pro')->get();

        $table = (new OrderDetailRemote)->getTable();
        $list = DB::table($table)->where(function($query){
            $query->select('id')->from('product')->whereColumn('id','order_detail.product_id')->limit(1);
        }, 'Pro')->get();

        $this->assertEquals($remoteList->toJson(JSON_NUMERIC_CHECK), json_encode($list, JSON_NUMERIC_CHECK));
    }

    public function testDelete()
    {
        $table = (new OrderDetailRemote)->getTable();
        $record = DB::table($table)->first();
        $rowsNum = OrderDetailRemote::destroy($record->od_id);
        
        $item = DB::table($table)->where('od_id', $record->od_id)->first();
        $this->assertEquals($rowsNum, 1);
        $this->assertEquals($item, null);
    }

    public function testCreate()
    {
        $table = (new OrderDetailRemote)->getTable();
        $remote = OrderDetailRemote::create([
            'o_id' => 1,
            'product_id' => 1,
        ]);
        $item = DB::table($table)->where('od_id', $remote->od_id)->select(['o_id', 'product_id', 'od_id'])->first();

        $this->assertEquals($remote->toJson(JSON_NUMERIC_CHECK), json_encode($item, JSON_NUMERIC_CHECK));
    }

    public function testUpdate()
    {
        $remote = OrderDetailRemote::first();
        $remote->product_id = 120;
        $remote->save();

        $this->assertEquals($remote->product_id, 120);

        $whereArr = ['od_id' => $remote->od_id];
        $updateArr = ['product_id' => 1];
        $item = OrderDetailRemote::updateOrCreate($whereArr, $updateArr);

        $this->assertEquals($item->product_id, 1);

    }

    public function testIncrement()
    {
        $remote = ProductRemote::first();
        ProductRemote::where('pid', $remote->pid)->increment('status', 1);

        $table = (new ProductRemote)->getTable();
        $status = DB::table($table)->where('pid', $remote->pid)->value('status'); 

        $this->assertEquals($remote->status + 1, $status);
    }
    
}
