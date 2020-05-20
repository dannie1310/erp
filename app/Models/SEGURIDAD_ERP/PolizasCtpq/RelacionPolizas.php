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
}