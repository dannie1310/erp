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
    /*protected $movimiento_a;
    protected $movimiento_b;*/
    protected $relacion;
    protected $id_busqueda;
    protected $busqueda;

    public function __construct(RelacionMovimientos $relacion, Busqueda $busqueda)
    {
        $this->relacion = $relacion;
        $this->id_busqueda = $busqueda->id;
        $this->busqueda = $busqueda;
        /*DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $relacion->base_datos_a);
        $this->movimiento_a = PolizaMovimiento::find($relacion->id_movimiento_a);
        $this->movimiento_a->load("cuenta");

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $relacion->base_datos_b);
        $this->movimiento_b = PolizaMovimiento::find($relacion->id_movimiento_b);
        $this->movimiento_b->load("cuenta");*/
    }

    public function buscarDiferenciasMovimientos()
    {
        $this->buscaDiferenciaConceptoMovimiento();
        /*print_r('7.1-'.date('Y-m-d H:i:s')."
                ");*/
        $this->buscaDiferenciaReferenciaMovimiento();
        /*print_r('7.2-'.date('Y-m-d H:i:s')."
                ");*/
        $this->buscaDiferenciaImporteMovimiento();
        /*print_r('7.3-'.date('Y-m-d H:i:s')."
                ");*/
        $this->buscaDiferenciaTipoMovimiento();
        /*print_r('7.4-'.date('Y-m-d H:i:s')."
                ");*/
        $this->buscaDiferenciaCodigoCuentaMovimiento();
        /*print_r('7.5-'.date('Y-m-d H:i:s')."
                ");*/
        $this->buscaDiferenciaNombreCuentaMovimiento();
        /*print_r('7.6-'.date('Y-m-d H:i:s')."
                ");*/

    }

    private function getInformacionDiferencia()
    {
        $datos_diferencia = [
            "id_busqueda" => $this->id_busqueda,
            "id_movimiento" => $this->relacion->id_movimiento_a,
            "id_poliza" => $this->relacion->id_poliza_a,
            "base_datos_revisada" => $this->relacion->base_datos_a,
            "base_datos_referencia" => $this->relacion->base_datos_b,
            "tipo_busqueda" => $this->relacion->tipo_relacion,
        ];
        return $datos_diferencia;
    }

    private function buscaDiferenciaCodigoCuentaMovimiento()
    {
        $lcodigo_a = strlen(($this->relacion->codigo_cuenta_a));
        $lcodigo_b = strlen(($this->relacion->codigo_cuenta_b));
        $codigo_a = ($this->relacion->codigo_cuenta_a);
        $codigo_b = ($this->relacion->codigo_cuenta_b);

        if ($lcodigo_b == 11 && $lcodigo_a == 13) {
            $codigo_a = ($this->relacion->codigo_cuenta_a);
            $codigo_b = ($this->relacion->codigo_cuenta_b);
            $g1 = substr($codigo_b, 0, 4);
            $g2 = substr($codigo_b, 4, 2);
            $g3 = substr($codigo_b, 6, 2);
            $g4 = substr($codigo_b, 8, 3);

            $codigo_b = $g1 . '0' . $g2 . '0' . $g3 . $g4;
        } else if ($lcodigo_a == 11 && $lcodigo_b == 13) {
            $codigo_a = ($this->relacion->codigo_cuenta_a);
            $codigo_b = ($this->relacion->codigo_cuenta_b);
            $g1 = substr($codigo_a, 0, 4);
            $g2 = substr($codigo_a, 4, 2);
            $g3 = substr($codigo_a, 6, 2);
            $g4 = substr($codigo_a, 8, 3);

            $codigo_a = $g1 . '0' . $g2 . '0' . $g3 . $g4;
        }

        if ($codigo_a != $codigo_b) {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 6;
            $datos_diferencia["valor_a"] = trim($this->relacion->codigo_cuenta_a);
            $datos_diferencia["valor_b"] = trim($this->relacion->codigo_cuenta_b);
            $datos_diferencia["observaciones"] = 'a: ' . trim($this->relacion->codigo_cuenta_a) . ' b: ' . trim($this->relacion->codigo_cuenta_b);
            Diferencia::registrar($datos_diferencia);
        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 6;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
        }
    }

    private function buscaDiferenciaNombreCuentaMovimiento()
    {
        if ((trim($this->relacion->nombre_cuenta_a) != trim($this->relacion->nombre_cuenta_b))) {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 7;
            $datos_diferencia["valor_a"] = trim($this->relacion->nombre_cuenta_a);
            $datos_diferencia["valor_b"] = trim($this->relacion->nombre_cuenta_b);
            $datos_diferencia["observaciones"] = 'a: ' . trim($this->relacion->nombre_cuenta_a) . ' b: ' . trim($this->relacion->nombre_cuenta_b);
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 7;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
        }
    }

    private function buscaDiferenciaConceptoMovimiento()
    {
        if ((trim($this->relacion->concepto_a) != trim($this->relacion->concepto_b))) {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 9;
            $datos_diferencia["valor_a"] = $this->relacion->concepto_a;
            $datos_diferencia["valor_b"] = $this->relacion->concepto_b;
            $datos_diferencia["observaciones"] = 'a: ' . $this->relacion->concepto_a . ' b: ' . $this->relacion->concepto_b;
            Diferencia::registrar($datos_diferencia);

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 9;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
        }
    }

    private function buscaDiferenciaReferenciaMovimiento()
    {
        if ((trim($this->relacion->referencia_a) != trim($this->relacion->referencia_b))) {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 8;
            $datos_diferencia["valor_a"] = $this->relacion->referencia_a;
            $datos_diferencia["valor_b"] = $this->relacion->referencia_b;
            $datos_diferencia["observaciones"] = 'a: ' . $this->relacion->referencia_a . ' b: ' . $this->relacion->referencia_b;
            Diferencia::registrar($datos_diferencia);
        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 8;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
        }
    }

    private function buscaDiferenciaImporteMovimiento()
    {
        if ($this->relacion->importe_a != $this->relacion->importe_b) {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 11;
            $datos_diferencia["valor_a"] = $this->relacion->importe_a;
            $datos_diferencia["valor_b"] = $this->relacion->importe_b;
            $datos_diferencia["observaciones"] = 'a: ' . $this->relacion->importe_a . ' b: ' . $this->relacion->importe_b;
            Diferencia::registrar($datos_diferencia);
        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 11;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
        }
    }

    private function buscaDiferenciaTipoMovimiento()
    {
        if ($this->relacion->tipo_movto_a != $this->relacion->tipo_movto_b) {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 10;
            $datos_diferencia["valor_a"] = $this->relacion->tipo_movto_a;
            $datos_diferencia["valor_b"] = $this->relacion->tipo_movto_b;
            $datos_diferencia["observaciones"] = 'a: ' . $this->relacion->tipo_movto_a . ' b: ' . $this->relacion->tipo_movto_b;
            Diferencia::registrar($datos_diferencia);
        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 10;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
        }
    }
}