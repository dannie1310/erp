<?php
namespace App\Informes\ContabilidadGeneral;

use App\Models\CADECO\PresupuestoObra\DatoConcepto;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativo;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SaldosNegativos
{
    public static function get(CuentaSaldoNegativo $cuenta)
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $cuenta->base_datos);
        $query = "select IdCuenta, MovimientosPoliza.Ejercicio, MovimientosPoliza.Periodo, sum (
          CASE MovimientosPoliza.TipoMovto
             WHEN 1 THEN MovimientosPoliza.Importe * -1
             WHEN 0 THEN MovimientosPoliza.Importe
          END) as Saldo
from dbo.MovimientosPoliza join dbo.Cuentas on(Cuentas.Id = MovimientosPoliza.IdCuenta)
where Cuentas.Id = ".$cuenta->id_cuenta."
group by IdCuenta, MovimientosPoliza.Ejercicio, MovimientosPoliza.Periodo order by Ejercicio, Periodo";


        $informe = DB::connection("cntpq")->select($query);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = SaldosNegativos::reorganizaArreglo($informe);
        return $informe;
    }

    public static function getMovimientos($data){
        $cuenta = CuentaSaldoNegativo::find($data["id"]);
        $meses = SaldosNegativos::getMeses();
        $informe["informe"]["encabezado"] = [
            "mes"=>ucfirst(strtolower($meses[$data["mes"]-1]["nombre_mes"]))
        ];

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $cuenta->base_datos);

        $query = "select Polizas.Id as IdPoliza, CONVERT(varchar,Polizas.Fecha,103) as Fecha, TiposPolizas.Nombre as Tipo, Polizas.Folio, MovimientosPoliza.Concepto, MovimientosPoliza.Referencia,
       CASE MovimientosPoliza.TipoMovto
             WHEN 0 THEN MovimientosPoliza.Importe
           ELSE 0
          END  Cargo,
CASE MovimientosPoliza.TipoMovto
             WHEN 1 THEN MovimientosPoliza.Importe
            ELSE 0
          END Abono,
        CASE MovimientosPoliza.TipoMovto
             WHEN 1 THEN MovimientosPoliza.Importe * -1
             WHEN 0 THEN MovimientosPoliza.Importe
          END Importe
from dbo.MovimientosPoliza
    join dbo.Polizas on(Polizas.Id = MovimientosPoliza.IdPoliza)
join dbo.TiposPolizas on(TiposPolizas.Id = MovimientosPoliza.TipoPol)
where MovimientosPoliza.IdCuenta = ".$cuenta->id_cuenta." and MovimientosPoliza.Ejercicio=".$data["anio"]." and MovimientosPoliza.Periodo = ".$data["mes"];
        $informe_data = DB::connection("cntpq")->select($query);
        $informe_data = array_map(function ($value) {
            return (array)$value;
        }, $informe_data);
        $saldo_inicial = SaldosNegativos::saldoInicial($data);

        $informe_data = SaldosNegativos::calculaSaldos($saldo_inicial, $informe_data);

        $informe["informe"]["data"]["movimientos"] = $informe_data;
        $informe["informe"]["data"]["saldo_inicial"] = $saldo_inicial;
        $informe["informe"]["data"]["saldo_inicial_format"] = '$'.number_format($saldo_inicial, 2,".",",");

        return $informe;
    }

    private static function calculaSaldos($saldo_inicial, $movimientos){
        $i = 0;
        $saldo = $saldo_inicial;
        foreach($movimientos as $movimiento){
            $saldo+=$movimiento["Importe"];
            $movimientos[$i]["CargoFormat"] = ($movimiento["Cargo"]==0)?"-":"$".number_format($movimiento["Cargo"],2,".",",");
            $movimientos[$i]["AbonoFormat"] = ($movimiento["Abono"]==0)?"-":"$".number_format($movimiento["Abono"],2,".",",");
            $movimientos[$i]["Saldo"] = $saldo;
            $movimientos[$i]["SaldoFormat"] = "$".number_format($saldo,2,".",",");
            $i++;
        }
        return $movimientos;
    }

    private static function saldoInicial($data)
    {
        $cuenta = CuentaSaldoNegativo::find($data["id"]);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $cuenta->base_datos);
        $fecha=$data["anio"].'/'.sprintf("%02d", $data["mes"])."/01";
        if($data["mes"]<=12){
            $query = "
                select sum (
                          CASE MovimientosPoliza.TipoMovto
                             WHEN 1 THEN MovimientosPoliza.Importe * -1
                             WHEN 0 THEN MovimientosPoliza.Importe
                          END) as Saldo
                from dbo.MovimientosPoliza join dbo.Cuentas on(Cuentas.Id = MovimientosPoliza.IdCuenta)
                where Cuentas.Id = ".$cuenta->id_cuenta." and Fecha < '".$fecha."'
            ";
        }
        else {
            $query = "
                select sum (
                          CASE MovimientosPoliza.TipoMovto
                             WHEN 1 THEN MovimientosPoliza.Importe * -1
                             WHEN 0 THEN MovimientosPoliza.Importe
                          END) as Saldo
                from dbo.MovimientosPoliza join dbo.Cuentas on(Cuentas.Id = MovimientosPoliza.IdCuenta)
                where Cuentas.Id = ".$cuenta->id_cuenta." and Ejercicio = '".$data["anio"]."' and Periodo<'".$data["mes"]."'
            ";
        }



        $informe = DB::connection("cntpq")->select($query);

        return $informe[0]->Saldo;

    }

    private static function reorganizaArreglo($informe)
    {
        $anio_inicial = $informe[0]["Ejercicio"];
        $mes_inicial = $informe[0]["Periodo"];
        $anio_final = $informe[count($informe)-1]["Ejercicio"];
        $anio_actual = Date("Y");
        $mes_actual = Date("m");
        $meses = SaldosNegativos::getMeses();
        $salida = [];
        $i = 0;
        foreach($informe as $item){
            $informe_reorganizado [$item["Ejercicio"].'_'.$item["Periodo"]] = $item["Saldo"] ;
            $i++;
        }
        $j = 0;
        $acumulado = 0;
        for($i = $anio_inicial; $i<=$anio_final; $i++) {
            foreach($meses as $mes){
                if($i == $anio_inicial)
                {
                    if($mes_inicial<=$mes["id"] ){
                        $salida[$j]["anio"] = $i;
                        $salida[$j]["mes_txt"] = $mes["mes_txt"];
                        $salida[$j]["mes"] = $mes["id"];
                        if(key_exists($i.'_'.$mes["id"], $informe_reorganizado)){
                            $acumulado += $informe_reorganizado[$i.'_'.$mes["id"]];
                            $salida[$j]["monto_format"] = "$".number_format($informe_reorganizado[$i.'_'.$mes["id"]],2,".",",");
                            $salida[$j]["saldo_format"] = "$".number_format($acumulado,2,".",",");
                            $salida[$j]["monto"] = $informe_reorganizado[$i.'_'.$mes["id"]];
                            $salida[$j]["saldo"] = $acumulado;
                        } else {
                            $salida[$j]["monto_format"] = "-";
                            $salida[$j]["saldo_format"] = "$".number_format($acumulado,2,".",",");
                            $salida[$j]["monto"] = 0;
                            $salida[$j]["saldo"] = $acumulado;
                        }
                        $j++;
                    }
                }
                 else if($i == $anio_actual){
                     if($mes["id"]<=$mes_actual){
                         $salida[$j]["anio"] = $i;
                         $salida[$j]["mes_txt"] = $mes["mes_txt"];
                         $salida[$j]["mes"] = $mes["id"];
                         if(key_exists($i.'_'.$mes["id"], $informe_reorganizado)){
                             $acumulado += $informe_reorganizado[$i.'_'.$mes["id"]];
                             $salida[$j]["monto_format"] = "$".number_format($informe_reorganizado[$i.'_'.$mes["id"]],2,".",",");
                             $salida[$j]["saldo_format"] = "$".number_format($acumulado,2,".",",");
                             $salida[$j]["monto"] = $informe_reorganizado[$i.'_'.$mes["id"]];
                             $salida[$j]["saldo"] = $acumulado;
                         } else {
                             $salida[$j]["monto_format"] = "-";
                             $salida[$j]["saldo_format"] = "$".number_format($acumulado,2,".",",");
                             $salida[$j]["monto"] = 0;
                             $salida[$j]["saldo"] = $acumulado;
                         }
                         $j++;
                     }
                 } else {
                    $salida[$j]["anio"] = $i;
                    $salida[$j]["mes_txt"] = $mes["mes_txt"];
                     $salida[$j]["mes"] = $mes["id"];
                    if(key_exists($i.'_'.$mes["id"], $informe_reorganizado)){
                        $acumulado += $informe_reorganizado[$i.'_'.$mes["id"]];
                        $salida[$j]["monto_format"] = "$".number_format($informe_reorganizado[$i.'_'.$mes["id"]],2,".",",");
                        $salida[$j]["saldo_format"] = "$".number_format($acumulado,2,".",",");
                        $salida[$j]["monto"] = $informe_reorganizado[$i.'_'.$mes["id"]];
                        $salida[$j]["saldo"] = $acumulado;
                    } else {
                        $salida[$j]["monto_format"] = "-";
                        $salida[$j]["saldo_format"] = "$".number_format($acumulado,2,".",",");
                        $salida[$j]["monto"] = 0;
                        $salida[$j]["saldo"] = $acumulado;
                    }
                    $j++;
                }
            }
        }

        return $salida;
    }

    private static function getMeses()
    {
        $query = "
select * from (
        SELECT
        CatalogoMeses.MesID AS mes_txt,
        CatalogoMeses.MesID AS nombre_mes,
        CatalogoMeses.MesID as id
        FROM SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
      union select '13', '13','13'
union select '14', '14','14') as sub
order by id


        ";/*union select '13', '13','13'
union select '14', '14','14'*/

        $meses = DB::select($query);
        $meses = array_map(function ($value) {
            return (array)$value;
        }, $meses);
        return $meses;
    }

}
