<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class OrderRemote extends RemoteModel {
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = 'o_id';
    protected $table = 'order';
    public $timestamps = false;


    public function orderDetails()
    {
        return $this->hasMany(OrderDetailRemote::class, 'o_id');
    }

    
}