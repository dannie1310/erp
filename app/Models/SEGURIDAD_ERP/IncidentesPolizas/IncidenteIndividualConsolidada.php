<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\IncidentesPolizas;


use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class IncidenteIndividualConsolidada extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.IncidentesPolizas.incidentes_individual_consolidada';
    public $timestamps = false;
    protected $fillable = [
        "id_poliza",
        "base_datos",
        "id_tipo_incidente",
        "fecha_hora_deteccion",
        "fecha_hora_resolucion"
    ];

    public function getFechaHoraDeteccionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_deteccion);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFechaHoraResolucionFormatAttribute()
    {
        if($this->fecha_hora_resolucion){
            $date = date_create($this->fecha_hora_resolucion);
            return date_format($date, "d/m/Y H:i:s");
        } else {
            return null;
        }

    }

    public function tipo_incidente()
    {
        return $this->belongsTo(CtgTipoIncidenteP::class,"id_tipo_incidente","id");
    }

    public function scopeActivos($query)
    {
        return $query->whereNull("fecha_hora_resolucion");
    }


    public function poliza()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database',$this->base_datos);
        return $this->belongsTo(Poliza::class, "id_poliza", "Id");
    }
}