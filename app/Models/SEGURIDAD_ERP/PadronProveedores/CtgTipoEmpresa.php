<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class CtgTipoEmpresa extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.ctg_tipos_empresas';
    public $timestamps = false;
}
