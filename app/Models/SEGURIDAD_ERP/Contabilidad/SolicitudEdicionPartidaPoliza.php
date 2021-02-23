<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:54 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'bd_contpaq','AliasBDD');
    }

    public function poliza()
    {
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database', $this->bd_contpaq);
        return $this->belongsTo(Poliza::class, "id_poliza", "Id");
    }

    public function getMontoFormatAttribute()
    {
        return '$ ' . number_format(abs($this->monto),2);
    }

    public function scopeAutorizadas($query)
    {
        return $query->where('solicitud_edicion_partida_polizas.estado', 1);
    }

}
