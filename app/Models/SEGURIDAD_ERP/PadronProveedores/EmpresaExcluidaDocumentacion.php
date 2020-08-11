<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class EmpresaExcluidaDocumentacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas_excluidas_documentacion';
    public $timestamps = false;
}
