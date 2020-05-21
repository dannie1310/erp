<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/05/2020
 * Time: 06:42 PM
 */

namespace App\Utils;


use App\Models\CTPQ\PolizaMovimiento;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionMovimientos;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Busqueda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;

class BusquedaDiferenciasMovimientos
{
    protected $movimiento_a;
    protected $movimiento_b;
    protected $relacion;
    protected $id_busqueda;

    public function __construct(RelacionMovimientos $relacion, Busqueda $busqueda) {
        $this->relacion = $relacion;
        $this->id_busqueda = $busqueda->id;
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $relacion->base_datos_a);
        $this->movimiento_a = PolizaMovimiento::find($relacion->id_movimiento_a);
        $this->movimiento_a->load("cuenta");

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $relacion->base_datos_b);
        $this->movimiento_b = PolizaMovimiento::find($relacion->id_movimiento_b);
        $this->movimiento_b->load("cuenta");
    }

    public function buscarDiferenciasMovimientos()
    {
        $this->buscaDiferenciaConceptoMovimiento();
        $this->buscaDiferenciaReferenciaMovimiento();
        $this->buscaDiferenciaImporteMovimiento();
        $this->buscaDiferenciaTipoMovimiento();
        $this->buscaDiferenciaCodigoCuentaMovimiento();
        $this->buscaDiferenciaNombreCuentaMovimiento();
        /*$this->buscaDiferenciaSumaImportesPoliza();
        $this->buscaDiferenciaNumMovimientosPoliza();*/

    }

    private function getInformacionDiferencia()
    {
        $datos_diferencia = [
            "id_busqueda" =>$this->id_busqueda,
            "id_movimiento" => $this->movimiento_a->Id,
            "id_poliza" => $this->movimiento_a->poliza->Id,
            "base_datos_revisada" => $this->relacion->base_datos_a,
            "base_datos_referencia" => $this->relacion->base_datos_b,
            "tipo_busqueda" => $this->relacion->tipo_relacion,
        ];
        return $datos_diferencia;
    }

    private function buscaDiferenciaCodigoCuentaMovimiento()
    {
        if((trim($this->movimiento_a->cuenta->Codigo) != trim($this->movimiento_b->cuenta->Codigo)))
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 6;
            $datos_diferencia["observaciones"] = 'a: ' . trim($this->movimiento_a->cuenta->Codigo) . ' b: ' . trim($this->movimiento_b->cuenta->Codigo);
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 6;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }

    private function buscaDiferenciaNombreCuentaMovimiento()
    {
        if((trim($this->movimiento_a->cuenta->Nombre) != trim($this->movimiento_b->cuenta->Nombre)))
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 7;
            $datos_diferencia["observaciones"] = 'a: ' . trim($this->movimiento_a->cuenta->Nombre) . ' b: ' . trim($this->movimiento_b->cuenta->Nombre);
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 7;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }

    private function buscaDiferenciaConceptoMovimiento()
    {
        if((trim($this->movimiento_a->Concepto) != trim($this->movimiento_b->Concepto)))
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 9;
            $datos_diferencia["observaciones"] = 'a: ' . $this->movimiento_a->Concepto . ' b: ' . $this->movimiento_b->Concepto;
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 9;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }

    private function buscaDiferenciaReferenciaMovimiento()
    {
        if((trim($this->movimiento_a->Referencia) != trim($this->movimiento_b->Referencia)))
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 8;
            $datos_diferencia["observaciones"] = 'a: ' . $this->movimiento_a->Referencia . ' b: ' . $this->movimiento_b->Referencia;
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 8;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }

    private function buscaDiferenciaImporteMovimiento()
    {
        if($this->movimiento_a->Importe != $this->movimiento_b->Importe)
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 11;
            $datos_diferencia["observaciones"] = 'a: ' . $this->movimiento_a->Importe . ' b: ' . $this->movimiento_b->Importe;
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 11;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }

    private function buscaDiferenciaTipoMovimiento()
    {
        if($this->movimiento_a->TipoMovto != $this->movimiento_b->TipoMovto)
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 10;
            $datos_diferencia["observaciones"] = 'a: ' . $this->movimiento_a->TipoMovto . ' b: ' . $this->movimiento_b->TipoMovto;
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 10;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }

    private function buscaDiferenciaSumaImportesPoliza()
    {
        if($this->poliza_a->Cargos != $this->poliza_b->Abonos)
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 3;
            $datos_diferencia["observaciones"] = 'a: ' . $this->poliza_a->Cargos . ' b: ' . $this->poliza_b->Abonos;
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 3;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }

    private function buscaDiferenciaNumMovimientosPoliza()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->relacion->base_datos_a);
        $movimientos_a = $this->poliza_a->movimientos()->orderBy("NumMovto")->get();

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->relacion->base_datos_b);
        $movimientos_b = $this->poliza_b->movimientos()->orderBy("NumMovto")->get();

        if(count($movimientos_a) != count($movimientos_b))
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 4;
            $datos_diferencia["observaciones"] = 'a: ' . count($movimientos_a) . ' b: ' . count($movimientos_b);
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 4;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }
        }
    }



}