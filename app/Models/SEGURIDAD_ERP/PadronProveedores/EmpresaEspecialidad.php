<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class EmpresaEspecialidad extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas_especialidades';
    public $timestamps = false;
    protected $fillable = [
        'id_empresa_proveedora',
        'id_especialidad'
    ];
}
