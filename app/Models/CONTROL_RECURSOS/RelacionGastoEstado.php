<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoEstado extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'series';
    protected $primaryKey = 'idseries';

}
