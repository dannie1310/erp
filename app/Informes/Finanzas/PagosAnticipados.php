<?php


namespace App\Informes\Finanzas;


use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\Reportes\CatalogoMeses;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PagosAnticipados
{
    public static function getIndicadorAplicadas($base_datos, $id_obra)
    {
        //pagos anticipados pagados / pagos anticipados registrados
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base_datos);

        $total_solicitudes = SolicitudPagoAnticipado::where("estado",">=",0)->count();
        $qry_total_solicitudes_con_orden_pago = DB::connection("cadeco")->select("
select count(*) as completas from (

          SELECT
    spa.id_transaccion,
    spa.tipo_transaccion,
    spa.opciones,
    spa.monto as monto_solicitud,
    sum(opag.monto) as monto_orden_pago,
    spa.monto+sum(opag.monto) as pendiente

FROM
    dbo.transacciones as spa
JOIN dbo.transacciones pag ON
    spa.id_transaccion = pag.id_antecedente and pag.tipo_transaccion =82
JOIN dbo.transacciones opag ON
    opag.numero_folio = pag.numero_folio and opag.tipo_transaccion = 68 and opag.id_obra = $id_obra
WHERE
    spa.tipo_transaccion = 72
    AND spa.opciones = 327681
   and spa.id_obra = $id_obra

   group by spa.id_transaccion,
    spa.tipo_transaccion,
    spa.opciones,
    spa.monto
       having spa.monto+sum(opag.monto)<0.9
       ) as completas


        ")
        ;
        $total_solicitudes_con_orden_pago = $qry_total_solicitudes_con_orden_pago[0]->completas;
        $razon_solicitudes = number_format($total_solicitudes_con_orden_pago / $total_solicitudes *100, "0") ;

        if($razon_solicitudes>=90)
        {
            $color = "bg-success";
        }else if($razon_solicitudes<90 && $razon_solicitudes>=50)
        {
            $color = "bg-warning";
        }else if($razon_solicitudes<50)
        {
            $color = "bg-danger";
        }

        return ["porcentaje"=>$razon_solicitudes,
            "aplicados"=> $total_solicitudes_con_orden_pago . " aplicadas",
        "pendientes"=> $total_solicitudes-$total_solicitudes_con_orden_pago . " pendientes",
        "dividendo" => $total_solicitudes . " registradas",
            "color" => $color
        ];
    }
    public static function  getInforme()
    {
        $informe["informe"][] = PagosAnticipados::getPartidasProyectoEmpresa();
        $informe["fechas"] = PagosAnticipados::getFechasInforme();
        return $informe;
    }

    public static function  getInformeEmpresaProyecto()
    {
        $informe["informe"][] = PagosAnticipados::getPartidasEmpresaProyecto();
        $informe["fechas"] = PagosAnticipados::getFechasInforme();
        return $informe;
    }

}
