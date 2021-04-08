<?php


namespace App\Models\SEGURIDAD_ERP\Documentacion;


use Illuminate\Database\Eloquent\Model;

class CtgTipoTransaccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Documentacion.ctg_tipos_transaccion';
    public $timestamps = false;

    public function tiposArchivo()
    {
        return $this->hasMany(TipoArchivoTipoTransaccion::class, "id_tipo_transaccion", "id");
    }

}
