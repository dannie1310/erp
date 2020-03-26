<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:54 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class SolicitudEdicionPartidaPoliza extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitud_edicion_partida_polizas';
    public $timestamps = false;
    protected $fillable =[
        "bd_contpaq"
        , "id_empresa_contpaq"
        , "id_poliza"
        , "concepto_original"
        , "monto"
    ];

    public function partida_solicitud()
    {
        return $this->belongsTo(SolicitudEdicionPartida::class,"id_solicitud_partida","id");
    }

    public function  movimientos()
    {
        return $this->hasMany(SolicitudEdicionPartidaPolizaMovimiento::class,"id_solicitud_partida_poliza","id");
    }

    public function getMontoFormatAttribute()
    {
        return '$ ' . number_format(abs($this->monto),2);
    }

    public function scopeAutorizadas($query)
    {
        return $query->where('estado', 1);
    }

}