<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpq;


use App\Models\CTPQ\PolizaMovimiento;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class RelacionMovimientos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpq.relaciones_movimientos';
    public $timestamps = false;
    protected $fillable =[
        "id_movimiento_a",
        "base_datos_a",
        "id_movimiento_b",
        "base_datos_b",
        "tipo_relacion",
        "sin_incidentes",
        "activa",
        "fecha_hora_verificacion",
        "num_movto_a",
        "num_movto_b",
        "tipo_movto_a",
        "tipo_movto_b",
        "codigo_cuenta_a",
        "codigo_cuenta_b",
        "nombre_cuenta_a",
        "nombre_cuenta_b",
        "importe_a",
        "importe_b",
        "referencia_a",
        "referencia_b",
        "concepto_a",
        "concepto_b",
        "id_poliza_a",
        "id_poliza_b",
        "id_cuenta_a",
        "id_cuenta_b"
    ];

    public function movimiento_revisado()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_a);
        return $this->belongsTo(PolizaMovimiento::class, "id_movimiento_a", "Id");
    }

    public function movimiento_referencia()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_b);
        return $this->belongsTo(PolizaMovimiento::class, "id_movimiento_b", "Id");
    }

    public function diferencias()
    {
        return $this->hasMany(Diferencia::class,"id_relacion_movimiento","id");
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
        $relacion = RelacionMovimientos::where("id_movimiento_a",$datos_relacion["id_movimiento_a"])
            ->where("id_movimiento_b",$datos_relacion["id_movimiento_b"])
            ->where("base_datos_a",$datos_relacion["base_datos_a"])
            ->where("base_datos_b",$datos_relacion["base_datos_b"])
            ->where("tipo_relacion",$datos_relacion["tipo_relacion"])
            ->first();
        if(!$relacion){
            $relacion = RelacionMovimientos::create($datos_relacion);
        }else{
            $relacion->update($datos_relacion);
        }
        return $relacion;
    }
}
