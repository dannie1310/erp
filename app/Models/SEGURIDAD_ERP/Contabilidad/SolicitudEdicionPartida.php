<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:49 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use Illuminate\Database\Eloquent\Model;

class SolicitudEdicionPartida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitud_edicion_partidas';
    public $timestamps = false;
    protected $fillable = [
        "concepto"
        , "referencia"
        , "fecha"
        , "folio"
        , "tipo"
        , "id_diferencia"
    ];

    public function scopeActivas($query)
    {
        return $query->whereHas('polizasAutorizadas')->orWhere("estado","1");
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudEdicion::class, "id_solicitud_edicion", "id");
    }

    public function diferencia()
    {
        return $this->belongsTo(Diferencia::class, "id_diferencia", "id");
    }

    public function polizas()
    {
        return $this->hasMany(SolicitudEdicionPartidaPoliza::class, "id_solicitud_partida", "id");
    }

    public function polizasAutorizadas()
    {
        return $this->hasMany(SolicitudEdicionPartidaPoliza::class, "id_solicitud_partida", "id")->autorizadas();
    }

    public function movimientos()
    {
        return $this->hasManyThrough(SolicitudEdicionPartidaPolizaMovimiento::class,SolicitudEdicionPartidaPoliza::class,"id_solicitud_partida","id_solicitud_partida_poliza","id","id");
    }

    public function getImporteFormatAttribute()
    {
        return "$ " . number_format($this->importe, 2, ".", ",");
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getTipoFormatAttribute()
    {
        switch ($this->tipo){
            case 1 :
                return 'Ingreso';
                break;
            case 2 :
                return 'Egreso';
                break;
            case 3 :
                return 'Diario';
                break;
        }
    }

    public function getNumeroBDAttribute()
    {
        $bd = [];
        $polizas = $this->polizas;
        if($polizas){
            foreach($polizas as $poliza){
                $bd[]= $poliza->bd_contpaq;
            }
        }
        $no_bd = count(array_unique($bd));
        return $no_bd;
    }

    public function getNumeroPolizasAttribute()
    {
        if($this->solicitud->id_tipo == 1)
        {
            return $this->polizas()->count();
        } else if($this->solicitud->id_tipo == 2 || $this->solicitud->id_tipo == 3)
        {
            return 1;
        } else {
            return '-';
        }
    }

    public function getNumeroPolizasFormatAttribute()
    {
        if(is_numeric($this->numero_polizas)){
            return number_format($this->numero_polizas,0,"",",");
        }
        else {
            return $this->numero_polizas;
        }
    }

    public function getNumeroMovimientosAttribute()
    {
        if($this->solicitud->id_tipo == 1)
        {
            $no_movimientos = 0;
            $polizas = $this->polizas;
            if($polizas){
                foreach($polizas as $poliza){
                    $no_movimientos+= $poliza->movimientos()->count();
                }
            }
            return $no_movimientos;
        } else if($this->solicitud->id_tipo == 2 || $this->solicitud->id_tipo == 3)
        {
            if($this->diferencia->id_movimiento>0){
                return 1;
            } else {
                return "-";
            }
        } else {
            return '-';
        }
    }

    public function getNumeroMovimientosFormatAttribute()
    {
        if(is_numeric($this->numero_movimientos)){
            return number_format($this->numero_movimientos,0,"",",");
        }
        else {
            return $this->numero_movimientos;
        }
    }

}