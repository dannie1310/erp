<?php

namespace App\Models\CTPQ\NmNominas;

use App\Models\CTPQ\NomGenerales\Nom10000;
use App\Models\MODULOSSAO\InterfazNominas\LogXmlPolizaNominaIFS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
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
    public function logPoliza()
    {
        return $this->belongsTo(LogXmlPolizaNominaIFS::class, 'id_poliza_contpaq', 'idpoliza');
    }

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

    public function getEstadoLogAttribute()
    {
       $empresa = Nom10000::where('RutaEmpresa', Config::get('database.connections.cntpq_nom.database'))->first();

       $log = LogXmlPolizaNominaIFS::where('empresa', $empresa->empresa_nombre)
           ->where('actividad', $empresa->actividad)
           ->where('id_poliza_contpaq', $this->getKey())->orderByRaw('fecha_hora_registro, estatus desc')->pluck('estatus')->first();
       return $log ? $log : "0";
    }

    public function getEstadoLogFormatAttribute()
    {
        if($this->estado_log == 0)
        {
            return 'PENDIENTE';
        }elseif ($this->estado_log == 1)
        {
            return 'DESCARGADO';
        }elseif ($this->estado_log == 2)
        {
            return 'ENVIADO';
        }
    }

    public function getColorEstadoLogAttribute()
    {
        if($this->estado_log == 0)
        {
            return '#FFFFFF';
        }elseif ($this->estado_log == 1)
        {
            return ' #efec4b';
        }elseif ($this->estado_log == 2)
        {
            return '#34ae3a';
        }
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
                '' [tramfrenra], '' [divisa],
                csegmentonegocio [depcate],
                '' [EMPLEADO], '' [INTERCOMPA], '' [ACTFIJO], '' [DEUDORES], '' [LIBRE],
                'MXN' [codigo_divisa],
                isnull(cargo,0) [debe],
                isnull(abono,0) [haber],
                '' [Referencia],
                LP.concepto [texto]
        from	[".$bd."].[dbo].[nom10015] LP,
                [".$bd."].[dbo].[nom10016] MP
        where	LP.idpoliza = ".$id."
        and		MP.idpoliza = LP.idpoliza"));
    }

    public function log($empresa, $estatus)
    {
        LogXmlPolizaNominaIFS::create([
            'empresa' => $empresa->empresa_nombre,
            'actividad' => $empresa->actividad,
            'id_poliza_contpaq' => $this->getKey(),
            'usuario_carga' => auth()->id(),
            'estatus' => $estatus
        ]);
    }
}
