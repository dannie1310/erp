<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;

use Illuminate\Database\Eloquent\Model;

class ProveedorREPUbicacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.vw_proveedor_rep_ubicaciones';
    public $timestamps = false;
    public $fillable = [
    ];

}
