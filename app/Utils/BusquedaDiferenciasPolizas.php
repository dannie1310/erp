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
    protected $relaciones_movimientos;

    public function __construct($relaciones, Busqueda $busqueda) {

        $this->relacion = $relaciones["relacion_poliza"];
        if(key_exists("relaciones_movimientos",$relaciones)) {
            $this->relaciones_movimientos = $relaciones["relaciones_movimientos"];
        } else {
            $this->relaciones_movimientos =[];
        }

        $this->id_busqueda = $busqueda->id;
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->relacion->base_datos_a);
        $this->poliza_a = Poliza::find($this->relacion->id_poliza_a);

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->relacion->base_datos_b);
        $this->poliza_b = Poliza::find($this->relacion->id_poliza_b);
    }

    public function buscarDiferenciasPolizas()
    {
        $this->buscaDiferenciaConceptoPoliza();
        $this->buscaDiferenciaSumaImportesPoliza();
        $diferencia_num_movtos = $this->buscaDiferenciaNumMovimientosPoliza();
        $diferencia_orden = false;
        if(!$diferencia_num_movtos){
            $diferencia_orden = $this->buscaDiferenciaOrdenMovimientosPoliza();
        }
        if($diferencia_num_movtos === true || $diferencia_orden === true)
        {
            return true;
        } else {
            return false;
        }
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
            /*$datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 2;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }*/
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 2;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
        }
    }

    private function buscaDiferenciaSumaImportesPoliza()
    {
        if(abs($this->poliza_a->Cargos - $this->poliza_b->Abonos) > 0.99)
        {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 3;
            $datos_diferencia["valor_a"] = $this->poliza_a->Cargos;
            $datos_diferencia["valor_b"] = $this->poliza_b->Abonos;
            $datos_diferencia["observaciones"] = 'a: ' . $this->poliza_a->Cargos . ' b: ' . $this->poliza_b->Abonos;
            Diferencia::registrar($datos_diferencia);

        } else {
            /*$datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 3;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }*/
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 3;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
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
            return true;

        } else {
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 4;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
            return false;
            /*$datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 4;
            $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
            if($diferencia_prexistente){
                $diferencia_prexistente->corregir($this->id_busqueda);
            }*/
        }
    }

    private function buscaDiferenciaOrdenMovimientosPoliza()
    {
        $arreglo_codigos_a = [];
        $arreglo_tipos_a = [];
        $arreglo_importes_a = [];

        $arreglo_codigos_b = [];
        $arreglo_tipos_b = [];
        $arreglo_importes_b = [];

        $cadena_a = "";
        $cadena_a_ordenada = "";
        $cadena_b = "";
        $cadena_b_ordenada = "";

        $arreglo_registros = [];


        $i = 0;
        foreach($this->relaciones_movimientos as $relacion_movimiento){
            $codigos = $this->igualaLongitudCodigos($relacion_movimiento->codigo_cuenta_a, $relacion_movimiento->codigo_cuenta_b);
            $arreglo_codigos_a[$i] = $codigos["codigo_a"];
            $arreglo_codigos_b[$i] = $codigos["codigo_b"];

            $arreglo_tipos_a[$i] = $relacion_movimiento->tipo_movto_a;
            $arreglo_tipos_b[$i] = $relacion_movimiento->tipo_movto_b;

            $arreglo_importes_a[$i] = $relacion_movimiento->importe_a;
            $arreglo_importes_b[$i] = $relacion_movimiento->importe_b;

            $arreglo_registros_a[$i]["codigo"] = $codigos["codigo_a"];
            $arreglo_registros_a[$i]["tipo"] = $relacion_movimiento->tipo_movto_a;
            $arreglo_registros_a[$i]["importe"] =  $relacion_movimiento->importe_a;

            $arreglo_registros_b[$i]["codigo"] = $codigos["codigo_b"];
            $arreglo_registros_b[$i]["tipo"] = $relacion_movimiento->tipo_movto_b;
            $arreglo_registros_b[$i]["importe"] =  $relacion_movimiento->importe_b;

            $cadena_a .= $codigos["codigo_a"] . $relacion_movimiento->tipo_movto_a . $relacion_movimiento->importe_a;
            $cadena_b .= $codigos["codigo_b"] . $relacion_movimiento->tipo_movto_b . $relacion_movimiento->importe_b;

            $i++;
        }

        $md5_a = md5($cadena_a);
        $md5_b = md5($cadena_b);
        if($md5_a == $md5_b){
            $datos_diferencia = $this->getInformacionDiferencia();
            $datos_diferencia["id_tipo"] = 12;
            Diferencia::corregir($datos_diferencia, $this->id_busqueda);
            return false;

        } else {

            foreach ($arreglo_registros_a as $clave => $fila) {
                $orden1[$clave] = $fila['codigo'];
                $orden2[$clave] = $fila['tipo'];
                $orden3[$clave] = $fila['importe'];
            }

            array_multisort($orden1, SORT_ASC, $orden2, SORT_ASC, $orden3, SORT_ASC, $arreglo_registros_a);
            dd($arreglo_registros_a);

            sort($arreglo_codigos_a);
            sort($arreglo_codigos_b);
            sort($arreglo_tipos_a);
            sort($arreglo_tipos_b);
            sort($arreglo_importes_a);
            sort($arreglo_importes_b);
            for($j=0; $j<$i; $j++){
                try{
                    $cadena_a_ordenada .= $arreglo_codigos_a[$j] . $arreglo_tipos_a[$j] . $arreglo_importes_a[$j];
                    $cadena_b_ordenada .= $arreglo_codigos_b[$j] . $arreglo_tipos_b[$j] . $arreglo_importes_b[$j];
                } catch (\Exception $e)
                {
                    //dd($i, $arreglo_codigos_a, $arreglo_codigos_b, $arreglo_tipos_a,$arreglo_tipos_b,$arreglo_importes_a,$arreglo_importes_b);
                }

            }
            $md5_a_o = md5($cadena_a_ordenada);
            $md5_b_o = md5($cadena_b_ordenada);
            if($md5_a_o == $md5_b_o){
                $datos_diferencia = $this->getInformacionDiferencia();
                $datos_diferencia["id_tipo"] = 12;
                $datos_diferencia["valor_a"] = $md5_a .' '. $md5_a_o;
                $datos_diferencia["valor_b"] = $md5_b .' '. $md5_b_o;
                $datos_diferencia["observaciones"] = 'a: ' . $md5_a .' '. $md5_a_o . ' b: ' . $md5_b .' '. $md5_b_o;
                Diferencia::registrar($datos_diferencia);
                return true;
            } else {
                return false;
            }
        }
    }
    private function igualaLongitudCodigos($codigo_a, $codigo_b){
        $lcodigo_a = strlen($codigo_a);
        $lcodigo_b = strlen($codigo_b);

        if ($lcodigo_b == 11 && $lcodigo_a == 13) {

            $g1 = substr($codigo_b, 0, 4);
            $g2 = substr($codigo_b, 4, 2);
            $g3 = substr($codigo_b, 6, 2);
            $g4 = substr($codigo_b, 8, 3);

            $codigo_b = $g1 . '0' . $g2 . '0' . $g3 . $g4;
        } else if ($lcodigo_a == 11 && $lcodigo_b == 13) {

            $g1 = substr($codigo_a, 0, 4);
            $g2 = substr($codigo_a, 4, 2);
            $g3 = substr($codigo_a, 6, 2);
            $g4 = substr($codigo_a, 8, 3);

            $codigo_a = $g1 . '0' . $g2 . '0' . $g3 . $g4;
        }
        return ["codigo_a"=>$codigo_a, "codigo_b"=>$codigo_b];
    }
    /*
     *
     * */
}