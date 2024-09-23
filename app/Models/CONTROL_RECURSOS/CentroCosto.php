<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'centroscosto';
    public $timestamps = false;
    protected $primaryKey = 'IdCC';
}
