<?php

namespace App\Models\REPSEG;

use Illuminate\Database\Eloquent\Model;

class VwFinIngresoRegistrado extends Model
{
    protected $connection = 'repseg';
    protected $table = 'vw_fin_ingreso_registrado';
    protected $primaryKey = 'idingreso_registrado';
}
