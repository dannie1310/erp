<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DiferenciaCorregida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.diferencias_corregidas';
    public $timestamps = false;
    protected $fillable = [
        "id_busqueda",
        "id_diferencia",
        "fecha_hora_deteccion",
    ];

    public function getFechaHoraDeteccionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_deteccion);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFechaHoraResolucionFormatAttribute()
    {
        if ($this->fecha_hora_resolucion) {
            $date = date_create($this->fecha_hora_resolucion);
            return date_format($date, "d/m/Y H:i:s");
        } else {
            return null;
        }

    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipo::class, "id_tipo", "id");
    }

    public function scopeActivos($query)
    {
        return $query->whereNull("fecha_hora_resolucion");
    }


    public function poliza()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos);
        return $this->belongsTo(Poliza::class, "id_poliza", "Id");
    }

    public function desactivar()
    {
        $this->activa = 0;
        $this->save();
    }
}