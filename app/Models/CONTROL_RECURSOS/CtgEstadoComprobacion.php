<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CtgEstadoComprobacion extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'ctg_estados_comprobaciones';
    protected $primaryKey = 'idctg_estados_comprobaciones';

}
