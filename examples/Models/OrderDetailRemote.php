<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class OrderDetailRemote extends RemoteModel {
    protected $pk = 'od_id';
    protected $table = 'order_detail';

    public function product()
    {
        return $this->belongsTo(ProductRemote::class, 'pid');
    }
    
}