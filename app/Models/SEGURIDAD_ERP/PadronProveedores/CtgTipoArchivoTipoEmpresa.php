<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class CtgTipoArchivoTipoEmpresa extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.ctg_tipos_archivos_ctg_tipos_empresas';
    public $timestamps = false;

    public function tipoArchivo(){
        return $this->belongsTo(CtgTipoArchivo::class, "id_tipo_archivo", "id");
    }

    public function tipoEmpresa(){
        return $this->belongsTo(CtgTipoEmpresa::class, "id_tipo_empresa", "id");
    }
}
