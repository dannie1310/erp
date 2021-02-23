<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 07/07/2020
 * Time: 07:44 PM
 */

namespace App\Informes;


use Illuminate\Support\Facades\DB;

class CFDEmpresaMes
{

    public static function get()
    {
        return CFDEmpresaMes::getArreglo();


    }

    private static function getArreglo()
    {
        $query = "SELECT ListaEmpresasSAT.id,
       ListaEmpresasSAT.razon_social,
       ListaEmpresasSAT.rfc,
       COUNT (cfd_sat.id) AS cantidad,
       month (cfd_sat.fecha) AS mes,
       year (cfd_sat.fecha) AS anio,
       CatalogoMeses.NombreCorto AS mes_txt,
       RIGHT ('00' + CONVERT (VARCHAR (2), month (fecha)), 2) AS mes_z,
       '[' + rfc + '] ' + razon_social AS cfd_rs
  FROM ((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
         INNER JOIN
         (SELECT cfd_sat.id
            FROM SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat,
                 (SELECT MAX (cfd_sat.fecha) AS fecha_fin,
                         DATEADD (month, -13, max (cfd_sat.fecha))
                            AS fecha_inicio
                    FROM SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat) Subquery
           WHERE cfd_sat.fecha BETWEEN Subquery.fecha_inicio
                                   AND Subquery.fecha_fin) Subquery
            ON (cfd_sat.id = Subquery.id))
        INNER JOIN
        SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
           ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
       INNER JOIN SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
          ON (month (cfd_sat.fecha) = CatalogoMeses.MesID)
          WHERE cfd_sat.cancelado != 1
GROUP BY ListaEmpresasSAT.razon_social,
         ListaEmpresasSAT.rfc,
         month (cfd_sat.fecha),
         year (cfd_sat.fecha),
         ListaEmpresasSAT.id,
         CatalogoMeses.NombreCorto";

        $informe = DB::select($query);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = CFDEmpresaMes::reorganizaArreglo($informe);
        return $informe;
    }

    private static function reorganizaArreglo($informe)
    {
        $empresas=[];
        $anio_mes=[];
        $valores= [];
        foreach ($informe as $item) {
            $empresas[$item["rfc"]]["valor"] = $item["cfd_rs"];
            $anio_mes[$item["anio"].$item["mes_z"]] = $item["mes_txt"].' '.$item["anio"];
            $valores[$item["rfc"]][$item["anio"].$item["mes_z"]] =($item["cantidad"]>0)?number_format($item["cantidad"],0,".",","):"-";
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
        return ["empresas"=>$empresas, "anios_meses"=>$anio_mes, "valores"=>$valores];
    }
}
