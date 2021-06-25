<?php


namespace App\Models\SEGURIDAD_ERP\Documentacion;


use Illuminate\Database\Eloquent\Model;

class TipoArchivoTipoTransaccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Documentacion.tipos_archivo_tipos_transaccion';
    public $timestamps = false;

    public function tipoArchivo()
    {
        return $this->belongsTo(CtgTipoArchivo::class, "id_tipo_archivo", "id");
    }

}
