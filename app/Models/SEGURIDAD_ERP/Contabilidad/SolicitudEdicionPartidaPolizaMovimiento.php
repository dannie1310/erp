<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:55 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class SolicitudEdicionPartidaPolizaMovimiento extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitud_edicion_partida_poliza_movimientos';
    public $timestamps = false;
    protected $fillable =[
        "id_solicitud_partida_poliza"
        , "id_movimiento"
        , "concepto_original"
        , "referencia_original"
    ];

    public function  partida_poliza()
    {
        return $this->belongsTo(SolicitudEdicionPartidaPoliza::class,"id_solicitud_partida_poliza","id");
    }

}