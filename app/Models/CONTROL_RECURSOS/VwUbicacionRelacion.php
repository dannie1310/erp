<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class VwUbicacionRelacion extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'vw_ubicaciones_relacion';
    protected $primaryKey = 'idubicacion';

}
