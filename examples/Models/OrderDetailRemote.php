<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class OrderDetailRemote extends RemoteModel {
    protected $primaryKey = 'od_id';
    protected $table = 'order_detail';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['store_id', 'o_id', 'product_id', 'status'];


    public function product()
    {
        return $this->belongsTo(ProductRemote::class, 'pid');
    }
    
}