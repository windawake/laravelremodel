<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class ProductRemote extends RemoteModel {
    protected $primaryKey = 'pid';
    protected $table = 'product';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    
}