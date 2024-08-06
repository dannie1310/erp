<?php

namespace App\Models\CTPQ\NmNominas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nom10015 extends Model
{
    protected $connection = 'cntpq_nom';
    protected $table = 'nom10015';
    protected $primaryKey = 'idpoliza';
    public $timestamps = false;

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopeConGuid($query )
    {
        return $query->whereRaw("GUIDPoliza <> '0'");
    }

    /**
     * Atributos
     */
    public function getEjercicioAttribute()
    {
        $date = date_create($this->fechapoliza);
        return date_format($date,"Y");
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fechapoliza);
        return date_format($date,"d/m/Y");
    }

    /**
     * Métodos
     */
    public static function datosPoliza($bd, $id)
    {
        \Config::set('database.connections.cntpq_nom.database',$bd);
        return DB::connection('cntpq_nom')->select(DB::raw("
                select  (
                select	concat(rfc,convert(VARCHAR,fechaconstitucion,12),homoclave)
                from	[".$bd."].[dbo].[nom10000]
                ) [company],
                year(LP.fechapoliza) [ejercicio_contable],
                convert(varchar,LP.fechapoliza,103) [fecha],
                'M' [Tipo_asiento],
                MP.cuentacw [cuenta],
                'XXXXX' [proyecto], -- Poner la columna de proyecto de la tabla de empresas
                '' [tramfrenra], '' [divisa],
                csegmentonegocio [depcate],
                '' [EMPLEADO], '' [INTERCOMPA], '' [ACTFIJO], '' [DEUDORES], '' [LIBRE],
                'MXN' [codigo_divisa],
                'XXXXXXXXX' [secuencia_actividad], -- Poner la columna de actividad de la tabla de empresas
                isnull(cargo,0) [debe],
                isnull(abono,0) [haber],
                '' [Referencia],
                LP.concepto [texto]
        from	[".$bd."].[dbo].[nom10015] LP,
                [".$bd."].[dbo].[nom10016] MP
        where	LP.idpoliza = ".$id."
        and		MP.idpoliza = LP.idpoliza"));
    }
}
