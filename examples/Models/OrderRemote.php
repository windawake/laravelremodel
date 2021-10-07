<?php
namespace Laravel\Remote2Model\Examples\Models;

use Laravel\Remote2Model\RemoteModel;

class OrderRemote extends RemoteModel {
    protected $pk = 'oid';
    protected $table = 'order';

    public function orderDetails()
    {
        return $this->hasMany(OrderDetailRemote::class, 'oid');
    }

    
}