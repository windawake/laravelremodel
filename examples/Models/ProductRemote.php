<?php
namespace Laravel\Remote2Model\Examples\Models;

use Laravel\Remote2Model\RemoteModel;

class ProductRemote extends RemoteModel {
    protected $pk = 'pid';
    protected $table = 'product';
    
}