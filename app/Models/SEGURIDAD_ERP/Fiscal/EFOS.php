<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 01/07/2020
 * Time: 05:12 PM
 */

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEstadosEfos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EFOS extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.efos';
    public $timestamps = false;

    public function efo()
    {
        return $this->belongsTo(CtgEfos::class,"rfc","rfc");
    }

    public function estadoEFOS()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado', 'id');
    }

    public function cfd()
    {
        return $this->hasMany(CFDSAT::class,"rfc_emisor", "rfc");
    }

    public function scopePresuntos($query){
        return $query->where('estado', '=', 2);
    }

    public function scopeDefinitivo($query){
        return $query->where('estado', '=', 0);
    }

    public static function  getInforme()
    {
        $presuntos = [];
        $autocorregidos = [];
        $informe[] = EFOS::getPartidasInformeDefinitivos();
        $informe[] = EFOS::getPartidasInformePresuntos();
        $informe[] = ["autocorregidos"=>$autocorregidos];
        return $informe;
    }

    private static function getPartidasInformePresuntos(){
        $informe = DB::select("SELECT ctg_estados_efos.descripcion AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       ListaEmpresasSAT.nombre_corto AS empresa,
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,
       SUM (cfd_sat.total) AS importe,
       format (sum (cfd_sat.total), 'C') AS importe_format
  FROM (((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
          INNER JOIN
          SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
             ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
         INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
            ON (efos.rfc = cfd_sat.rfc_emisor))
        INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_estados_efos ctg_estados_efos
           ON (efos.estado = ctg_estados_efos.id))
       INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
          ON (ctg_efos.rfc = efos.rfc)
 WHERE (efos.estado = 2)
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto
ORDER BY empresa ASC, ctg_efos.fecha_presunto DESC")
            ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOS::setSubtotalesPartidas($informe, "PRESUNTOS");
        return $informe;
    }

    private static function getPartidasInformeDefinitivos(){
        $informe = [];
        $empresas = Empresa::getEmpresaDefinitivos();
        $i=0;
        $efos = [];
        foreach($empresas as $empresa){
            $comprobantes = $empresa->cfd()->deEFO()->noAutocorregidos()->get();
            if(count($comprobantes)>0){
                foreach($comprobantes as $comprobante){
                    $efos[$comprobante->efo->razon_social] = $comprobante->efo;
                }
                ksort($efos);
                $efos = array_unique($efos);
                foreach($efos as $efo){
                    if($efo->estado==0)
                    {
                        $comprobantes_efo = $efo->cfd()->noAutoCorregidos()->where("rfc_receptor",$empresa->rfc)->get();
                        if(count($comprobantes_efo)>0){
                            $informe[$i]["empresa"] = $empresa->nombre_corto;
                            $informe[$i]["rfc"] = $efo->rfc;
                            $informe[$i]["razon_social"] = $efo->razon_social;
                            $informe[$i]["fecha_presunto"] = $efo->efo->fecha_presunto_format;
                            $informe[$i]["fecha_definitivo"] = $efo->efo->fecha_definitivo_format;
                            $informe[$i]["estatus"] = $efo->estadoEFOS->descripcion;
                            $informe[$i]["no_CFDI"] = count($comprobantes_efo);
                            $informe[$i]["importe"] = $comprobantes_efo->sum("total");
                            $informe[$i]["importe_format"] = "$ ". number_format($comprobantes_efo->sum("total"),2,".", ",");
                            $i++;
                        }
                    }
                }
            }
        }
        $informe = EFOS::setSubtotalesPartidas($informe, "DEFINITIVOS");
        return $informe;
    }
    private static function setSubtotalesPartidas($partidas, $tipo){
        $partidas_completas = [];
        $i = 0;
        $contador_partidas_empresa = 1;
        $contador_cfdi = 0;
        $importe_cfdi=0;
        $contador_cfdi_global = 0;
        $importe_cfdi_global=0;
        $i_bis = 1;
        $i_p =0;
        $acumulador_partidas_empresa = 0;
        foreach($partidas as $partida)
        {
            if($i_p>0){
                if($partida["empresa"]!=$partidas[$i_p-1]["empresa"] ){
                    if($acumulador_partidas_empresa>1){
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
                    }
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
        return $partidas_completas;
    }
}