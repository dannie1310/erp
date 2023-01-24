<?php


namespace App\Informes\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfos;
use Illuminate\Support\Facades\DB;

class PendientesREPEmpresa
{
    public static function  get($data)
    {
        $informe["informe"] = PendientesREPEmpresa::getPartidas();

        return $informe;
    }


    private static function getPartidas(){
        $informe = DB::select("
SELECT
    les.rfc AS rfc_empresa,
    les.razon_social as empresa,
    count(DISTINCT cs.id) as cantidad_cfdi,
    sum( cs.total) as total_cfdi,
    sum( csrp.total_pagado) as total_rep,
    sum( csrp.pendiente_pago) as pendiente_rep

    FROM
    SEGURIDAD_ERP.Contabilidad.cfd_sat cs
INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat_rep_pendiente csrp ON
    cs.id = csrp.id_cfdi
INNER JOIN SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT les ON
    cs.id_empresa_sat = les.id

    GROUP BY

    les.rfc, les.razon_social

ORDER BY pendiente_rep DESC
    ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = PendientesREPEmpresa::setTitulosYSubtotalesPartidas($informe);
        return $informe;
    }


    private static function setTitulosYSubtotalesPartidas($partidas){

        $partidas_completas = [];
        if(count($partidas)>0){
            $i = 0;
            $contador_partidas_proveedor = 1;
            $contador_cfdi = 0;
            $importe_cfdi=0;
            $total_cfdi =0;
            $total_rep =0;
            $pendiente_rep =0;
            $contador_cfdi_global = 0;
            $importe_cfdi_global=0;
            $total_cfdi_global =0;
            $total_rep_global =0;
            $pendiente_rep_global =0;
            $i_bis = 1;
            $i_p =0;
            $acumulador_partidas_proveedor = 0;
            $acumulador_pendiente_rep = 0;

            foreach($partidas as $partida)
            {
                $partidas_completas[$i] = $partida;
                $partidas_completas[$i]["cantidad_cfdi_f"] = number_format($partidas_completas[$i]["cantidad_cfdi"],0);
                $partidas_completas[$i]["total_cfdi_f"] = number_format($partidas_completas[$i]["total_cfdi"],0);
                $partidas_completas[$i]["total_rep_f"] = number_format($partidas_completas[$i]["total_rep"],0);
                $partidas_completas[$i]["pendiente_rep_f"] = number_format($partidas_completas[$i]["pendiente_rep"],0);

                $partidas_completas[$i]["indice"] = $i_bis;
                $partidas_completas[$i]["tipo"] = "partida";

                $contador_cfdi+=$partidas_completas[$i]["cantidad_cfdi"];
                $importe_cfdi+=$partidas_completas[$i]["pendiente_rep"];
                $total_cfdi+=$partidas_completas[$i]["total_cfdi"];
                $total_rep += $partidas_completas[$i]["total_rep"];
                $pendiente_rep += $partidas_completas[$i]["pendiente_rep"];
                $contador_cfdi_global+= $partidas_completas[$i]["cantidad_cfdi"];
                $importe_cfdi_global += $partidas_completas[$i]["pendiente_rep"];

                $total_cfdi_global += $partidas_completas[$i]["total_cfdi"];
                $total_rep_global += $partidas_completas[$i]["total_rep"];
                $pendiente_rep_global += $partidas_completas[$i]["pendiente_rep"];


                $acumulador_pendiente_rep += $partidas_completas[$i]["pendiente_rep"];

                $partidas_completas[$i]["acumulado_pendiente_rep"] = $acumulador_pendiente_rep;
                $partidas_completas[$i]["acumulado_pendiente_rep_f"] = number_format($acumulador_pendiente_rep,0);

                $partidas_completas[$i]["porcentaje"] = 0;

                $contador_partidas_proveedor++;
                $i++;
                $i_bis++;
                $i_p++;
                $acumulador_partidas_proveedor++;


            }

            $partidas_completas[$i]["contador"] = $i_bis-1;
            $partidas_completas[$i]["etiqueta"] = "TOTAL ";
            $partidas_completas[$i]["contador_cfdi"] = $contador_cfdi_global;
            $partidas_completas[$i]["importe"] = $importe_cfdi_global;
            $partidas_completas[$i]["importe_format"] = '$ '.number_format($importe_cfdi_global,2,".",",");

            $partidas_completas[$i]["cantidad_cfdi_f"] = number_format($contador_cfdi_global);
            $partidas_completas[$i]["total_cfdi_f"] = number_format($total_cfdi_global);
            $partidas_completas[$i]["total_rep_f"] = number_format($total_rep_global);
            $partidas_completas[$i]["pendiente_rep_f"] = number_format($pendiente_rep_global);

            $partidas_completas[$i]["tipo"] = "total";
            $partidas_completas[$i]["bg_color_hex"] = "#757575";
            $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
            $partidas_completas[$i]["color_hex"] = "#FFF";
            $partidas_completas[$i]["color_rgb"] = [255,255,255];
        }

        return PendientesREPEmpresa::establecePorcentajePartidas($partidas_completas);
    }

    private static function establecePorcentajePartidas($partidas_completas)
    {

        $total = $partidas_completas[count($partidas_completas)-3]["acumulado_pendiente_rep"];
        $i = 0;
        foreach ($partidas_completas as $partida_completa)
        {
            if(key_exists("porcentaje", $partida_completa))
            {
                $partidas_completas[$i]["porcentaje"] = number_format($partida_completa["acumulado_pendiente_rep"] * 100 / $total,0);
            }
            $i++;
        }

        return $partidas_completas;
    }
}
