<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class ProveedorServicioIFS extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'proveedores_servicios_ifs';
    protected $primaryKey = 'id';
}
