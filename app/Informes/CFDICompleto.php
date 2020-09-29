<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 07/07/2020
 * Time: 07:44 PM
 */

namespace App\Informes;


use Illuminate\Support\Facades\DB;

class CFDICompleto
{

    public static function get()
    {
        return CFDICompleto::getArreglo();

    }

    private static function getArreglo()
    {
        $query = "
        SELECT ListaEmpresasSAT.id,
       ListaEmpresasSAT.razon_social,
       ListaEmpresasSAT.rfc,
       COUNT (cfd_sat.id) AS cantidad,
       SUM(cfd_sat.total) as total,
       month (cfd_sat.fecha) AS mes,
       year (cfd_sat.fecha) AS anio,
       CatalogoMeses.NombreCorto AS mes_txt,
       RIGHT ('00' + CONVERT (VARCHAR (2), month (fecha)), 2) AS mes_z,
       '[' + rfc + '] ' + razon_social AS cfd_rs
  FROM (SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
         INNER JOIN
        SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
           ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
       INNER JOIN SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
          ON (month (cfd_sat.fecha) = CatalogoMeses.MesID)
GROUP BY ListaEmpresasSAT.razon_social,
         ListaEmpresasSAT.rfc,
         month (cfd_sat.fecha),
         year (cfd_sat.fecha),
         ListaEmpresasSAT.id,
         CatalogoMeses.NombreCorto
ORDER BY razon_social asc , anio desc, mes asc";

        $informe = DB::select($query);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = CFDICompleto::reorganizaArreglo($informe);
        return $informe;
    }

    private static function getMeses()
    {
        $query = "
        SELECT
        CatalogoMeses.NombreCorto AS mes_txt,
        CatalogoMeses.MesID as id
        FROM SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
        ";

        $meses = DB::select($query);
        $meses = array_map(function ($value) {
            return (array)$value;
        }, $meses);
        return $meses;
    }

    private static function reorganizaArreglo($informe)
    {
        $empresas=[];
        $meses = CFDICompleto::getMeses();
        $anio_mes=[];
        $valores= [];
        $anio_empresa = [];
        foreach ($informe as $item) {
            $empresas[$item["rfc"]]["valor"] = $item["cfd_rs"];
            $anio_mes[$item["anio"].$item["mes_z"]] = $item["mes_txt"].' '.$item["anio"];
            $valores[$item["rfc"]][$item["anio"]][$item["mes"]]["cantidad"] =($item["cantidad"]>0)?number_format($item["cantidad"],0,".",","):"-";
            $valores[$item["rfc"]][$item["anio"]][$item["mes"]]["total"] =($item["total"]>0)?'$'.number_format($item["total"],2,".",","):"-";
            if(!key_exists("totales",$valores[$item["rfc"]][$item["anio"]]))
            {
                $valores[$item["rfc"]][$item["anio"]]["totales"]["cantidad"] = 0;
                $valores[$item["rfc"]][$item["anio"]]["totales"]["total"] = 0;
            }
            $valores[$item["rfc"]][$item["anio"]]["totales"]["cantidad"] += $item["cantidad"];
            $valores[$item["rfc"]][$item["anio"]]["totales"]["total"] += $item["total"];

            $valores[$item["rfc"]][$item["anio"]]["totales"]["cantidad_f"] = number_format($valores[$item["rfc"]][$item["anio"]]["totales"]["cantidad"],0,"",",");
            $valores[$item["rfc"]][$item["anio"]]["totales"]["total_f"] = "$".number_format($valores[$item["rfc"]][$item["anio"]]["totales"]["total"],2,".",",");

            if(!key_exists("totales",$valores[$item["rfc"]]))
            {
                $valores[$item["rfc"]]["totales"]["cantidad"] = 0;
                $valores[$item["rfc"]]["totales"]["total"] = 0;
            }
            $valores[$item["rfc"]]["totales"]["cantidad"] += $item["cantidad"];
            $valores[$item["rfc"]]["totales"]["total"] += $item["total"];

            $valores[$item["rfc"]]["totales"]["cantidad_f"] = number_format($valores[$item["rfc"]]["totales"]["cantidad"],0,"",",");
            $valores[$item["rfc"]]["totales"]["total_f"] = "$".number_format($valores[$item["rfc"]]["totales"]["total"],2,".",",");

            #TOTAL EMPRESA
            if(!key_exists($item["mes"],$valores[$item["rfc"]]["totales"]))
            {
                $valores[$item["rfc"]]["totales"][$item["mes"]]["cantidad"] = 0;
                $valores[$item["rfc"]]["totales"][$item["mes"]]["total"] = 0;
            }

            $valores[$item["rfc"]]["totales"][$item["mes"]]["cantidad"] += $item["cantidad"];
            $valores[$item["rfc"]]["totales"][$item["mes"]]["total"] += $item["total"];

            $valores[$item["rfc"]]["totales"][$item["mes"]]["cantidad_f"] = number_format($valores[$item["rfc"]]["totales"][$item["mes"]]["cantidad"],0,"",",");
            $valores[$item["rfc"]]["totales"][$item["mes"]]["total_f"] = "$".number_format($valores[$item["rfc"]]["totales"][$item["mes"]]["total"],2,".",",");

            #TOTAL GLOBAL

            if(!key_exists("totales",$valores))
            {
                $valores["totales"] = [];
            }

            if(!key_exists($item["mes"],$valores["totales"]))
            {
                $valores["totales"][$item["mes"]]["cantidad"] = 0;
                $valores["totales"][$item["mes"]]["total"] = 0;
                $valores["totales"]["cantidad"] = 0;
                $valores["totales"]["total"] = 0;
            }

            $valores["totales"][$item["mes"]]["cantidad"] += $item["cantidad"];
            $valores["totales"][$item["mes"]]["total"] += $item["total"];

            $valores["totales"][$item["mes"]]["cantidad_f"] = number_format($valores["totales"][$item["mes"]]["cantidad"],0,"",",");
            $valores["totales"][$item["mes"]]["total_f"] = "$".number_format($valores["totales"][$item["mes"]]["total"],2,".",",");

            $valores["totales"]["cantidad"] += $item["cantidad"];
            $valores["totales"]["total"] += $item["total"];

            $valores["totales"]["cantidad_f"] = number_format($valores["totales"]["cantidad"],0,"",",");
            $valores["totales"]["total_f"] = "$".number_format($valores["totales"]["total"],2,".",",");



            $anios_empresa_t[$item["rfc"]][] = $item["anio"];
        }


        foreach ($anios_empresa_t as $k=>$v){
            $anios_empresa[$k] = array_unique($v);
            $anios_empresa[$k] = array_values($anios_empresa[$k]);
        }

        ksort($empresas);
        krsort($anio_mes);
        array_pop($anio_mes);
        ksort($anio_mes);
        $i = 1;
        foreach ($empresas as $k=>$v){
            $empresas[$k]["k"] = $i;
            $i++;
        }
        return ["empresas"=>$empresas, "anios_meses"=>$anio_mes, "valores"=>$valores, "meses"=>$meses, "anios_empresa"=>$anios_empresa];
    }
}
