<?php

namespace App\Models\SEGURIDAD_ERP\PadronProveedores;

use Illuminate\Database\Eloquent\Model;

class SolicitudContraoferta extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.revires';
    public $timestamps = false;

    protected $fillable = [
        'base_datos',
        'id_obra',
        'id_usuario',
        'nombre_obra'
    ];

}
