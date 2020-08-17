<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class EmpresaPrestadora extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas_prestadoras';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa_proveedor',
        'id_empresa_prestadora',
    ];
}
