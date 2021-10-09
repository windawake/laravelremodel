<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class OrderRemote extends RemoteModel {
    protected $primaryKey = 'oid';
    protected $table = 'order';

    const CREATED_AT = null;
    const UPDATED_AT = null;


    public function orderDetails()
    {
        return $this->hasMany(OrderDetailRemote::class, 'oid');
    }

    
}