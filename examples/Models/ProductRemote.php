<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class ProductRemote extends RemoteModel {
    protected $pk = 'pid';
    protected $table = 'product';
    
}