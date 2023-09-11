<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGasto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos';
    protected $primaryKey = 'idrelaciones_gastos';
}
