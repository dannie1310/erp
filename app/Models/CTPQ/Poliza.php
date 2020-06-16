<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionMovimientos;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use Illuminate\Support\Facades\Config;


class Poliza extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Polizas';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, 'IdPoliza', 'Id');
    }

    public function cuentas()
    {
        return $this->hasManyThrough(Cuenta::class, PolizaMovimiento::class, "IdPoliza", "Id","Id", "IdCuenta");
    }

    public function tipo_poliza()
    {
        return $this->belongsTo(TipoPoliza::class, 'TipoPol', 'Id');
    }

    public function logs()
    {
        return $this->hasMany(LogEdicion::class, 'id_poliza', 'Id');
    }

    public function getCargosFormatAttribute()
    {
        return '$ ' . number_format(abs($this->Cargos), 2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date, "d/m/Y");
    }

    public function getFechaMesLetraFormatAttribute()
    {
        setlocale(LC_ALL,"es_ES");
        $fecha =New DateTime($this->Fecha);
        return strftime("%d/",$fecha->getTimestamp()).substr(ucfirst(strftime("%b",$fecha->getTimestamp())), 0, 3).strftime("/%Y",$fecha->getTimestamp());
    }

    public function actualiza($datos)
    {
        if ($this->Ejercicio != 2015) {
            try {
                DB::connection('cntpq')->beginTransaction();
                $this->Concepto = $datos["concepto"];
                $this->update();
                foreach ($datos["movimientos"] as $datos_movimiento) {
                    $movimiento = PolizaMovimiento::find($datos_movimiento["id"]);
                    $movimiento->Referencia = $datos_movimiento["referencia"];
                    $movimiento->Concepto = $datos_movimiento["concepto"];
                    $movimiento->update();
                }
                DB::connection('cntpq')->commit();
            } catch (\Exception $e) {
                DB::connection('cntpq')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        }
    }

    public function getPolizaRelacionada($busqueda)
    {
        $poliza_referencia = null;
        try {
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $busqueda->base_datos_referencia);
            $poliza_referencia = Poliza::where("Ejercicio", $this->Ejercicio)->where("Periodo", $this->Periodo)
                ->where("TipoPol", $this->TipoPol)->where("Folio", $this->Folio)->first();
        } catch (\Exception $e) {

        }
        return $poliza_referencia;
    }

    public function relaciona($busqueda)
    {
        $poliza_relacionada = $this->getPolizaRelacionada($busqueda);
        $relaciones = [];
        if ($poliza_relacionada) {
            $datos_relacion = [
                "id_poliza_a" => $this->Id,
                "base_datos_a" => $busqueda->base_datos_busqueda,
                "id_poliza_b" => $poliza_relacionada->Id,
                "base_datos_b" => $busqueda->base_datos_referencia,
                "tipo_relacion" => $busqueda->id_tipo_busqueda,
                "folio" => $this->Folio,
                "ejercicio" => $this->Ejercicio,
                "periodo" => $this->Periodo,
                "tipo" => $this->TipoPol
            ];
            $relacion_poliza = RelacionPolizas::registrar($datos_relacion);
            $relaciones_movimientos = $this->relaciona_movimientos($busqueda);
            if ($relacion_poliza) {
                $relaciones["relacion_poliza"] = $relacion_poliza;
                $this->resuelveIncidenteNoEncontrada($busqueda);
            }
            if ($relaciones_movimientos) {
                $relaciones["relaciones_movimientos"] = $relaciones_movimientos;
            }
        } else {
            $this->registraIncidenteNoEncontrada($busqueda);
        }
        return $relaciones;
    }

    private function resuelveIncidenteNoEncontrada($busqueda){
        $datos_diferencia = [
            "id_poliza" => $this->Id,
            "base_datos_revisada" => $busqueda->base_datos_busqueda,
            "base_datos_referencia" => $busqueda->base_datos_referencia,
            "id_tipo" => 1,
            "tipo_busqueda" => $busqueda->id_tipo_busqueda,
            "id_busqueda"=>$busqueda->id,
        ];
        $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
        if($diferencia_prexistente){
            $diferencia_prexistente->corregir($busqueda->id);
        }
    }

    private function registraIncidenteNoEncontrada($busqueda){
        $datos_diferencia = [
            "id_poliza" => $this->Id,
            "base_datos_revisada" => $busqueda->base_datos_busqueda,
            "base_datos_referencia" => $busqueda->base_datos_referencia,
            "id_tipo" => 1,
            "tipo_busqueda" => $busqueda->id_tipo_busqueda,
            "observaciones" => "",
            "id_busqueda"=>$busqueda->id,
        ];
        Diferencia::registrar($datos_diferencia);
    }

    private function relaciona_movimientos($busqueda)
    {
        $relaciones_movimientos = [];
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $busqueda->base_datos_busqueda);
        $movimientos = $this->movimientos()->orderBy("NumMovto")->get();
        $poliza_relacionada = $this->getPolizaRelacionada($busqueda);
        $movimientos_referencia = $poliza_relacionada->movimientos()->orderBy("NumMovto")->get();
        if (count($movimientos) == count($movimientos_referencia)) {
            $i = 0;
            foreach ($movimientos as $movimiento) {
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $busqueda->base_datos_busqueda);
                $movimiento->load("cuenta");
                //$movimiento->load("poliza");

                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $busqueda->base_datos_referencia);
                $movimientos_referencia[$i]->load("cuenta");
                //$movimientos_referencia[$i]->load("poliza");
                try{

                $datos_relacion = [
                    "id_movimiento_a" => $movimiento->Id,
                    "base_datos_a" => $busqueda->base_datos_busqueda,
                    "id_movimiento_b" => $movimientos_referencia[$i]->Id,
                    "base_datos_b" => $busqueda->base_datos_referencia,
                    "tipo_relacion" => $busqueda->id_tipo_busqueda,
                    "num_movto_a" => $movimiento->NumMovto,
                    "num_movto_b" => $movimientos_referencia[$i]->NumMovto,
                    "tipo_movto_a" => $movimiento->TipoMovto,
                    "tipo_movto_b" => $movimientos_referencia[$i]->TipoMovto,
                    "codigo_cuenta_a" => $movimiento->cuenta->Codigo,
                    "codigo_cuenta_b" => $movimientos_referencia[$i]->cuenta->Codigo,
                    "nombre_cuenta_a" => $movimiento->cuenta->Nombre,
                    "nombre_cuenta_b" => $movimientos_referencia[$i]->cuenta->Nombre,
                    "importe_a" => $movimiento->Importe,
                    "importe_b" => $movimientos_referencia[$i]->Importe,
                    "referencia_a" => $movimiento->Referencia,
                    "referencia_b" => $movimientos_referencia[$i]->Referencia,
                    "concepto_a" => $movimiento->Concepto,
                    "concepto_b" => $movimientos_referencia[$i]->Concepto,
                    "id_poliza_a" => $movimiento->IdPoliza,
                    "id_poliza_b" => $movimientos_referencia[$i]->IdPoliza,
                    "id_cuenta_a" => $movimiento->IdCuenta,
                    "id_cuenta_b" => $movimientos_referencia[$i]->IdCuenta,
                ];}
                catch (\Exception $e){
                   // dd($busqueda->base_datos_busqueda,$movimiento);
                }
                $relaciones_movimientos[$i] = RelacionMovimientos::registrar($datos_relacion);
                $i++;
            }
        }
        return $relaciones_movimientos;
    }

    public function sumaMismoPadreCargos($codigo_padre)
    {
        $suma = 0;
        foreach ($this->movimientos()->where("TipoMovto","=",0)->orderBy('IdCuenta')->get() as $movimiento)
        {
            if(substr($codigo_padre, 0,4) == substr($movimiento->cuenta->Codigo, 0, 4))
            {
                $suma = $suma + $movimiento->Importe;
            }
        }
        return $suma;
    }

    public function sumaMismoPadreAbonos($codigo_padre)
    {
        $suma = 0;
        foreach ($this->movimientos()->where("TipoMovto","=",1)->orderBy('IdCuenta')->get() as $movimiento)
        {
            if(substr($codigo_padre, 0,4) == substr($movimiento->cuenta->Codigo, 0, 4))
            {
                $suma = $suma + $movimiento->Importe;
            }
        }
        return $suma;
    }

    public function getConceptoPropuesta(SolicitudEdicion $solicitud_edicion){
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo","=","2")->toArray());
        if(count($diferencias) > 0){
            return $diferencias[0]["valor_b"];

        } else {
            return $this->Concepto;
        }
    }

    public function getCuentasPadresAttribute()
    {
        $cuentas_padres = [];
        foreach($this->cuentas()->orderBy("NumMovto")->get() as $cuenta)
        {
            $cuentas_padres [] = $cuenta->cuenta_padre;
        }
        return array_unique($cuentas_padres);
    }

    public function getPrimerMovimiento(Cuenta $cuenta_padre)
    {
        $codigo_padre = $cuenta_padre->Codigo;
        //dd();
        $movimientos = $this->movimientos()->orderBy('NumMovto', 'asc')->get();
        $primer_movimiento = "";
        foreach($movimientos as $movimiento)
        {
            if(substr($codigo_padre, 0,4) == substr($movimiento->cuenta->Codigo, 0, 4))
            {
                $primer_movimiento = $movimiento;
                break;
            }
        }
        return $primer_movimiento;
    }

    public function getMovimientos(Cuenta $cuenta_padre)
    {
        $codigo_padre = $cuenta_padre->Codigo;
        $movimientos = $this->movimientos()->orderBy('NumMovto', 'asc')->get();
        $movimientos_cuenta_padre = [];
        foreach($movimientos as $movimiento)
        {
            if(substr($codigo_padre, 0,4) == substr($movimiento->cuenta->Codigo, 0, 4))
            {
                $movimientos_cuenta_padre[] = $movimiento;
            }
        }
        return $movimientos_cuenta_padre;
    }
}
