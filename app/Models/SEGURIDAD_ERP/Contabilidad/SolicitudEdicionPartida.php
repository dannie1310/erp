<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:49 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class SolicitudEdicionPartida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitud_edicion_partidas';
    public $timestamps = false;
    protected $fillable =[
        "concepto"
        ,"referencia"
        ,"fecha"
        ,"folio"
        ,"tipo"
        ,"importe"
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudEdicion::class,"id_solicitud_edicion","id");
    }

    public function polizas()
    {
        return $this->hasMany(SolicitudEdicionPartidaPoliza::class,"id_solicitud_partida","id");
    }

}