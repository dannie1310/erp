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
    protected $busqueda;

    public function __construct(RelacionMovimientos $relacion, Busqueda $busqueda) {
        $this->relacion = $relacion;
        $this->id_busqueda = $busqueda->id;
        $this->busqueda;
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
        $lcodigo_a = strlen(trim($this->movimiento_a->cuenta->Codigo));
        $lcodigo_b = strlen(trim($this->movimiento_b->cuenta->Codigo));
        if($lcodigo_a == $lcodigo_b){
            $codigo_a = trim($this->movimiento_a->cuenta->Codigo);
            $codigo_b = trim($this->movimiento_b->cuenta->Codigo);
        } else if($lcodigo_b == 11 && $lcodigo_a == 13){
            $codigo_a = trim($this->movimiento_a->cuenta->Codigo);
            $codigo_b = trim($this->movimiento_b->cuenta->Codigo);
            $g1 = substr($codigo_b,0,4);
            $g2 = substr($codigo_b,4,2);
            $g3 = substr($codigo_b,6,2);
            $g4 = substr($codigo_b,8,3);

            $codigo_b = $g1.'0'.$g2.'0'.$g3 . $g4;
        } else {
            $codigo_a = trim($this->movimiento_a->cuenta->Codigo);
            $codigo_b = trim($this->movimiento_b->cuenta->Codigo);
        }

        if($codigo_a != $codigo_b)
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 6;
            $datos_diferencia["valor_a"] = trim($this->movimiento_a->cuenta->Codigo);
            $datos_diferencia["valor_b"] = trim($this->movimiento_b->cuenta->Codigo);
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
            $datos_diferencia["valor_a"] = trim($this->movimiento_a->cuenta->Nombre);
            $datos_diferencia["valor_b"] = trim($this->movimiento_b->cuenta->Nombre);
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
            $datos_diferencia["valor_a"] = $this->movimiento_a->Concepto;
            $datos_diferencia["valor_b"] = $this->movimiento_b->Concepto;
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
            $datos_diferencia["valor_a"] = $this->movimiento_a->Referencia;
            $datos_diferencia["valor_b"] = $this->movimiento_b->Referencia;
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
            $datos_diferencia["valor_a"] = $this->movimiento_a->Importe;
            $datos_diferencia["valor_b"] = $this->movimiento_b->Importe;
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
            $datos_diferencia["valor_a"] = $this->movimiento_a->TipoMovto;
            $datos_diferencia["valor_b"] = $this->movimiento_b->TipoMovto;
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

    private function getCodigoCuentaValidacion($codigo)
    {
        if(in_array($this->busqueda,[2,3]))
        {

        }
        return $codigo;
    }
}