<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CtgEstadoRelacion extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'ctg_estados_relaciones';
    protected $primaryKey = 'idctg_estados_relaciones';
}
