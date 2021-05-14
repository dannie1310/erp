<?php


namespace App\Informes\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use Illuminate\Support\Facades\DB;

class NoLocalizadosInforme
{
    public static function  getInforme()
    {
        $informe["informe"][] = NoLocalizadosInforme::getPartidasInformeDefinitivos();

        $informe["fechas"] = NoLocalizadosInforme::getFechasInforme();
        return $informe;
    }

    private static function getFechasInforme()
    {
        $fechas["lista_no_localizados"]= ProcesamientoListaNoLocalizados::getFechaUltimaCarga();
        $fechas["cfd_recibidos"]= CFDSAT::getFechaUltimoCFDTxt();
        return $fechas;
    }

    private static function getPartidasInformeDefinitivos(){
        $informe = DB::select("
        SELECT 'No Localizado' AS estatus,
       no_localizados.rfc,
       no_localizados.razon_social,
       ctg_no_localizados.entidad_federativa,
       ListaEmpresasSAT.nombre_corto AS empresa,
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,
       format (
          sum (
             CASE cfd_sat.tipo_comprobante
                WHEN 'I' THEN cfd_sat.total
                WHEN 'E' THEN cfd_sat.total * -1
             END),
          'C')
          AS importe_format,
       sum (
          CASE cfd_sat.tipo_comprobante
             WHEN 'I' THEN cfd_sat.total
             WHEN 'E' THEN cfd_sat.total * -1
          END)
          AS importe
  FROM ((((((SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
             INNER JOIN
             SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
               ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
           INNER JOIN SEGURIDAD_ERP.Fiscal.no_localizados no_localizados
              ON (no_localizados.rfc = cfd_sat.rfc_emisor)
       INNER JOIN (select * from Fiscal.ctg_no_localizados where estado = 1) as ctg_no_localizados
              ON (no_localizados.rfc = ctg_no_localizados.rfc)
      )
         )
        )
        LEFT OUTER JOIN
        SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones cfd_sat_autocorrecciones
           ON (cfd_sat_autocorrecciones.id_cfd_sat = cfd_sat.id))
       LEFT OUTER JOIN SEGURIDAD_ERP.Fiscal.cfd_no_deducidos cfd_no_deducidos
          ON (cfd_no_deducidos.id_cfd_sat = cfd_sat.id)
           )

 WHERE
       (no_localizados.estado = 1)
       AND (cfd_sat_autocorrecciones.id IS NULL)
       AND (cfd_no_deducidos.id IS NULL)
       AND (cfd_sat.estado !=8)
       AND cfd_sat.cancelado != 1
       AND cfd_sat.tipo_comprobante != 'P'
GROUP BY
         no_localizados.rfc,
         no_localizados.razon_social,
         ListaEmpresasSAT.nombre_corto,
         ctg_no_localizados.entidad_federativa

ORDER BY ListaEmpresasSAT.nombre_corto ASC;
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = NoLocalizadosInforme::setSubtotalesPartidas($informe, "NO LOCALIZADOS");
        return $informe;
    }

    private static function setSubtotalesPartidas($partidas, $tipo){
        $partidas_completas = [];
        if(count($partidas)>0){
            $i = 0;
            $contador_partidas_empresa = 1;
            $contador_cfdi = 0;
            $importe_cfdi=0;
            $contador_cfdi_global = 0;
            $importe_cfdi_global=0;
            $i_bis = 1;
            $i_p =0;
            $acumulador_partidas_empresa = 0;
            $partidas_completas[$i]["etiqueta"] = $tipo;
            $partidas_completas[$i]["tipo"] = "titulo";
            $partidas_completas[$i]["bg_color_hex"] = "#757575";
            $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
            $partidas_completas[$i]["color_hex"] = "#FFF";
            $partidas_completas[$i]["color_rgb"] = [255,255,255];
            $i++;
            foreach($partidas as $partida)
            {
                if($i_p>0){
                    if($partida["empresa"]!=$partidas[$i_p-1]["empresa"] ){
                        //if($acumulador_partidas_empresa>1){
                        $partidas_completas[$i]["contador"] = $contador_partidas_empresa-1;
                        $partidas_completas[$i]["acumulador"] = $acumulador_partidas_empresa;
                        $partidas_completas[$i]["etiqueta"] = "SUBTOTAL ".$tipo." ".$partidas[$i_p-1]["empresa"];
                        $partidas_completas[$i]["contador_cfdi"] = $contador_cfdi;
                        $partidas_completas[$i]["importe"] = $importe_cfdi;
                        $partidas_completas[$i]["importe_format"] = '$ '.number_format($importe_cfdi,2,".",",");
                        $partidas_completas[$i]["tipo"] = "subtotal";
                        $partidas_completas[$i]["bg_color_hex"] = "#d5d5d5";
                        $partidas_completas[$i]["bg_color_rgb"] = [213,213,213];
                        $partidas_completas[$i]["color_hex"] = "#000";
                        $partidas_completas[$i]["color_rgb"] = [0,0,0];
                        $i++;
                        //}
                        $contador_partidas_empresa = 1;
                        $contador_cfdi=0;
                        $importe_cfdi=0;
                        $acumulador_partidas_empresa=0;
                    }
                }

                $partidas_completas[$i] = $partida;
                $partidas_completas[$i]["indice"] = $i_bis;
                $partidas_completas[$i]["tipo"] = "partida";
                $contador_cfdi+=$partidas_completas[$i]["no_CFDI"];
                $importe_cfdi+=$partidas_completas[$i]["importe"];
                $contador_cfdi_global+=$partidas_completas[$i]["no_CFDI"];;
                $importe_cfdi_global+=$partidas_completas[$i]["importe"];
                $contador_partidas_empresa++;
                $i++;
                $i_bis++;
                $i_p++;
                $acumulador_partidas_empresa++;
            }

            $partidas_completas[$i]["contador"] = $contador_partidas_empresa-1;
            $partidas_completas[$i]["acumulador"] = $acumulador_partidas_empresa;
            $partidas_completas[$i]["etiqueta"] = "SUBTOTAL ".$tipo." ".$partidas[count($partidas)-1]["empresa"];
            $partidas_completas[$i]["contador_cfdi"] = $contador_cfdi;
            $partidas_completas[$i]["importe"] = $importe_cfdi;
            $partidas_completas[$i]["importe_format"] = '$ '.number_format($importe_cfdi,2,".",",");
            $partidas_completas[$i]["tipo"] = "subtotal";
            $partidas_completas[$i]["bg_color_hex"] = "#d5d5d5";
            $partidas_completas[$i]["bg_color_rgb"] = [213,213,213];
            $partidas_completas[$i]["color_hex"] = "#000";
            $partidas_completas[$i]["color_rgb"] = [0,0,0];
            $i++;


            $partidas_completas[$i]["contador"] = $i_bis-1;
            $partidas_completas[$i]["etiqueta"] = "TOTAL ".$tipo;
            $partidas_completas[$i]["contador_cfdi"] = $contador_cfdi_global;
            $partidas_completas[$i]["importe"] = $importe_cfdi_global;
            $partidas_completas[$i]["importe_format"] = '$ '.number_format($importe_cfdi_global,2,".",",");
            $partidas_completas[$i]["tipo"] = "total";
            $partidas_completas[$i]["bg_color_hex"] = "#757575";
            $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
            $partidas_completas[$i]["color_hex"] = "#FFF";
            $partidas_completas[$i]["color_rgb"] = [255,255,255];
        }

        return $partidas_completas;
    }

}
