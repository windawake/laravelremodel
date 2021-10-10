<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class ProductRemote extends RemoteModel {
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = 'pid';
    protected $table = 'product';
    public $timestamps = false;
    
}