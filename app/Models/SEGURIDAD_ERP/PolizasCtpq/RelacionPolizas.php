<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpq;


use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Facades\BusquedaDiferenciasPolizas;

class RelacionPolizas extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpq.relaciones_polizas';
    public $timestamps = false;
    protected $fillable =[
        "id_poliza_a",
        "base_datos_a",
        "id_poliza_b",
        "base_datos_b",
        "tipo_relacion",
        "sin_incidentes",
        "activa",
        "fecha_hora_verificacion"
    ];

    public function poliza_revisada()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_a);
        return $this->belongsTo(Poliza::class, "id_poliza_a", "Id");
    }

    public function poliza_referencia()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_b);
        return $this->belongsTo(Poliza::class, "id_poliza_b", "Id");
    }

    public function diferencias()
    {
        return $this->hasMany(Diferencia::class,"id_relacion_poliza","id");
    }

    public function scopeIndividualConsolidada($query)
    {
        return $query->where("tipo_relacion",1);
    }

    public function scopeIndividualHistorica($query)
    {
        return $query->where("tipo_relacion",2);
    }

    public function scopeConsolidadoraHistorica($query)
    {
        return $query->where('tipo_relacion', '=', 3);
    }

    public static function registrar($datos_relacion)
    {
        $relacion = RelacionPolizas::where("id_poliza_a", $datos_relacion["id_poliza_a"])
            ->where("id_poliza_b", $datos_relacion["id_poliza_b"])
            ->where("base_datos_a", $datos_relacion["base_datos_a"])
            ->where("base_datos_b", $datos_relacion["base_datos_b"])
            ->where("tipo_relacion", $datos_relacion["tipo_relacion"])
            ->first();
        if (!$relacion) {
            $relacion = RelacionPolizas::create($datos_relacion);
        }
        return $relacion;
    }
}