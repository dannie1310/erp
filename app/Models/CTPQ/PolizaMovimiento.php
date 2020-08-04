<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 12:11 PM
 */

namespace App\Models\CTPQ;

use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartida;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartidaPoliza;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;

class PolizaMovimiento extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'MovimientosPoliza';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Id',
        'IdPoliza',
        'Ejercicio',
        'Periodo',
        'TipoPol',
        'Folio',
        'NumMovto',
        'IdCuenta',
        'TipoMovto',
        'Importe',
        'Referencia',
        'Concepto',
        'Fecha',
        'TimeStamp',
        'Guid'
    ];

    public function poliza()
    {
        return $this->belongsTo(Poliza::class, 'IdPoliza', 'Id');
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'IdCuenta', 'Id');
    }

    public function logs()
    {
        return $this->hasMany(LogEdicion::class, 'id_movimiento', 'Id');
    }

    public function  getCargoAttribute(){
        if($this->TipoMovto == 0)
        {
            return $this->Importe;
        } else {
            return 0;
        }
    }

    public function  getAbonoAttribute(){
        if($this->TipoMovto == 1)
        {
            return $this->Importe;
        } else {
            return 0;
        }
    }

    public function  getCargoFormatAttribute(){
        if($this->cargo!=0){
            return "$ " . number_format($this->cargo,2,".",",");
        }
        else {
            return "-";
        }

    }

    public function  getAbonoFormatAttribute(){
        if($this->abono!=0){
            return "$ " . number_format($this->abono,2,".",",");
        }
        else {
            return "-";
        }
    }

    public function  getImporteComaFormatAttribute()
    {
        return number_format($this->Importe,2,".",",");
    }

    public function  getImporteFormatAttribute(){
        return "$ " . number_format($this->Importe,2,".",",");
    }

    public function getTipoFormatAttribute()
    {
        switch ($this->TipoMovto){
            case 0 :
                return 'Cargo';
                break;
            case 1 :
                return 'Abono';
                break;
        }
    }

    public function getReferenciaPropuesta(SolicitudEdicionPartida $solicitud_partida){
        if($solicitud_partida->referencia == ""){
            return $this->Referencia;
        } else {
            return $solicitud_partida->referencia;
        }
    }

    public function getConceptoPropuesta(SolicitudEdicion $solicitud_edicion){
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo","=","9")->where("id_movimiento","=",$this->Id)->toArray());
        if(count($diferencias) > 0){
            return $diferencias[0]["valor_b"];
        } else {
            return $this->Concepto;
        }
    }

    public function getConceptoOriginalT2(SolicitudEdicion $solicitud_edicion){
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo","=","9")->where("id_movimiento","=",$this->Id)->toArray());
        if(count($diferencias) > 0){
            return $diferencias[0]["valor_a"];
        } else {
            return $this->Concepto;
        }
    }

    public function getReferenciaPropuestaT2(SolicitudEdicion $solicitud_edicion){
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo","=","8")->where("id_movimiento","=",$this->Id)->toArray());
        if(count($diferencias) > 0){
            return $diferencias[0]["valor_b"];
        } else {
            return $this->Referencia;
        }
    }

    public function getReferenciaOriginalT2(SolicitudEdicion $solicitud_edicion){
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo","=","8")->where("id_movimiento","=",$this->Id)->toArray());
        if(count($diferencias) > 0){
            return $diferencias[0]["valor_a"];
        } else {
            return $this->Referencia;
        }
    }

    public function getConceptoOriginalT1(SolicitudEdicionPartidaPoliza $poliza){
        $movimiento = $poliza->movimientos->where("id_movimiento", $this->Id)->first();
        return $movimiento->concepto_original;
    }

    public function getReferenciaOriginalT1(SolicitudEdicionPartidaPoliza $poliza){
        $movimiento = $poliza->movimientos->where("id_movimiento", $this->Id)->first();
        return $movimiento->referencia_original;
    }

    public function createLog($campo, $valor_original, $valor_modificado)
    {
        $this->logs()->create([
            'id_poliza' => $this->IdPoliza,
            'id_campo' => $campo,
            'valor_original' => $valor_original,
            'valor_modificado' => $valor_modificado,
            'id_movimiento' => $this->Id
        ]);
    }

    public function getNuevoIdAttribute()
    {
      return self::orderBy('Id', 'desc')->first()->Id + 1;
    }
}
