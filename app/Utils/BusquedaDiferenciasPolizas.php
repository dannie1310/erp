<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/05/2020
 * Time: 06:42 PM
 */

namespace App\Utils;


use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Busqueda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;

class BusquedaDiferenciasPolizas
{
    protected $poliza_a;
    protected $poliza_b;
    protected $relacion;
    protected $id_busqueda;

    public function __construct(RelacionPolizas $relacion, Busqueda $busqueda) {
        $this->relacion = $relacion;
        $this->id_busqueda = $busqueda->id;
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $relacion->base_datos_a);
        $this->poliza_a = Poliza::find($relacion->id_poliza_a);

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $relacion->base_datos_b);
        $this->poliza_b = Poliza::find($relacion->id_poliza_b);
    }

    public function buscarDiferenciasPolizas()
    {
        $this->buscaDiferenciaConceptoPoliza();
        $this->buscaDiferenciaSumaImportesPoliza();
        $this->buscaDiferenciaNumMovimientosPoliza();

    }

    private function getInformacionDiferencia()
    {
        $datos_diferencia = [
            "id_busqueda" =>$this->id_busqueda,
            "id_poliza" => $this->poliza_a->Id,
            "base_datos_revisada" => $this->relacion->base_datos_a,
            "base_datos_referencia" => $this->relacion->base_datos_b,
            "tipo_busqueda" => $this->relacion->tipo_relacion,
        ];
        return $datos_diferencia;
    }

    private function buscaDiferenciaConceptoPoliza()
    {
        if((trim($this->poliza_a->Concepto) != trim($this->poliza_b->Concepto)))
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 2;
            $datos_diferencia["valor_a"] = $this->poliza_a->Concepto;
            $datos_diferencia["valor_b"] = $this->poliza_b->Concepto;
            $datos_diferencia["observaciones"] = 'a: ' . $this->poliza_a->Concepto . ' b: ' . $this->poliza_b->Concepto;
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 2;
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
            $datos_diferencia["valor_a"] = $this->poliza_a->Cargos;
            $datos_diferencia["valor_b"] = $this->poliza_b->Abonos;
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
            $datos_diferencia["valor_a"] = count($movimientos_a);
            $datos_diferencia["valor_b"] = count($movimientos_b);
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