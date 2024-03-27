<?php


namespace App\Informes\Finanzas;


use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
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
        $razon_solicitudes = 0;
        if($total_solicitudes_con_orden_pago != 0){
            $razon_solicitudes = number_format($total_solicitudes_con_orden_pago / $total_solicitudes *100, "0") ;
        }

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

    public static function getSolicitudesPagoAnticipadoParaIndicador(ConfiguracionObra $obra)
    {
        $nombre_obra = $obra->nombre;
        $base_datos = $obra->proyecto->base_datos;
        $id_obra = $obra->id_obra;
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base_datos);

        $pdo = DB::connection('cadeco')->getPdo();
        $pdo->exec('SET ANSI_WARNINGS ON');
        $pdo->exec('SET ANSI_PADDING ON');
        $pdo->exec('SET ANSI_NULLS ON');

        $solicitudes_para_indicador = DB::connection("cadeco")->select("
SELECT
       '".$base_datos."' as base_datos
       , '".$nombre_obra."' as nombre_obra
       , solicitudes_pago_sao.*
       , r.FolioRemesa as remesa_relacionada
       , vurdscm.IDDocumento as id_documento_remesa
       , vurdscm.MontoAutorizado as monto_autorizado_remesa

       FROM (

SELECT

spa.id_obra,
spa.id_transaccion,
spa.fecha as fecha_solicitud,
spa.numero_folio,
spa.opciones,
spa.monto as monto,
abs(isnull(pag.monto,0)) as monto_pagado,
abs(sum(isnull(opag.monto,0))) as monto_aplicado,
isnull(spa.monto,0)+sum(isnull(opag.monto,0)) as pendiente,
e.razon_social,
spa.id_usuario,

substring(replace(replace(spa.comentario,'SAO|',''),'SCR|',''),
(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1),
abs(CHARINDEX('|',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1))-(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1))
) as usuario_comentario,

case
	when usuario_int2.nombre_completo is not null then usuario_int2.nombre_completo
	when usuario_int1.nombre_completo is not null then usuario_int1.nombre_completo
else spa.comentario collate Latin1_General_CI_AS
end usuario_registro,
spa.observaciones

FROM
dbo.transacciones as spa
LEFT JOIN dbo.transacciones pag ON
spa.id_transaccion = pag.id_antecedente and pag.tipo_transaccion =82
LEFT JOIN dbo.transacciones opag ON
opag.numero_folio = pag.numero_folio and opag.tipo_transaccion = 68 and opag.id_obra = ".$id_obra."
LEFT JOIN dbo.empresas as e on e.id_empresa = spa.id_empresa

left join SEGURIDAD_ERP.dbo.vwUsuariosIntranetCompletos as usuario_int1
on usuario_int1.usuario collate Latin1_General_CI_AS
= substring(
replace(replace(spa.comentario,'SAO|',''),'SCR|',''),(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1),abs(CHARINDEX('|',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1))-(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1))
)

left join SEGURIDAD_ERP.dbo.vwUsuariosIntranetCompletos as usuario_int2
on usuario_int2.idusuario = spa.id_usuario

WHERE
spa.tipo_transaccion = 72
AND spa.opciones = 327681
and spa.id_obra = ".$id_obra."
and spa.estado >=0

group by spa.id_obra,
spa.id_transaccion,
spa.tipo_transaccion,
spa.opciones,
spa.monto,
pag.monto,
spa.numero_folio,
spa.fecha,
e.razon_social,
spa.id_usuario,

substring(replace(replace(spa.comentario,'SAO|',''),'SCR|',''),(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1),abs(CHARINDEX('|',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1))-(CHARINDEX(';',replace(replace(spa.comentario,'SAO|',''),'SCR|',''),5)+1)))

,
case
	when usuario_int2.nombre_completo is not null then usuario_int2.nombre_completo
	when usuario_int1.nombre_completo is not null then usuario_int1.nombre_completo
else spa.comentario collate Latin1_General_CI_AS
end,
spa.comentario,
spa.observaciones

)

AS solicitudes_pago_sao

left join SEGURIDAD_ERP.Finanzas.vwUltimaRemesaDocumentoSAOConMonto vurdscm on(
vurdscm.IDTransaccionCDC= solicitudes_pago_sao.id_transaccion and
vurdscm.IDObra = solicitudes_pago_sao.id_obra and
vurdscm.BaseDatos = '".$base_datos."')


left join SEGURIDAD_ERP.Finanzas.vwRemesas as r
on r.IDRemesa = vurdscm.IDRemesa

        ")
        ;
        $solicitudes_para_indicador = array_map(function ($value) {
            return (array)$value;
        }, $solicitudes_para_indicador);

        $solicitudes_completas = PagosAnticipados::complementaDatosRemesa($solicitudes_para_indicador);

        return $solicitudes_completas;
    }


    public static function complementaDatosRemesa($solicitudes)
    {
        $i = 0;
        foreach($solicitudes as $solicitud)
        {

            $datos_remesa = DB::connection("modulosao")->select("
SELECT
       vurdscm.FolioRemesa as remesa_relacionada
       , vurdscm.IDDocumento as id_documento_remesa
       , vurdscm.MontoAutorizado as monto_autorizado_remesa

       FROM
(

SELECT r.IDRemesa AS IDRemesa ,d.IDDocumento AS IDDocumento
, d.IDTransaccionCDC, upo.id_obra AS IDObra, bdo.BaseDatos,
dp.MontoAutorizadoPrimerEnvio + MontoAutorizadoSegundoEnvio as MontoAutorizado,
       cast(r.Anio as varchar(4))+'-'+
cast(r.NumeroSemana as varchar(4)) +'-'+
rtr.TipoRemesa + '-'+
cast(r.Folio as varchar(4)) AS FolioRemesa
   FROM
    [ModulosSAO].[ControlRemesas].[Documentos] d
INNER JOIN [ModulosSAO].[ControlRemesas].[Remesas] r ON
    d.IDRemesa = r.IDRemesa
INNER JOIN [ModulosSAO].[Proyectos].[Proyectos] p ON
    r.IDProyecto = p.IDProyecto
INNER JOIN [ModulosSAO].[dbo].[UnificacionProyectoObra] upo ON
    upo.IDProyecto = p.IDProyecto
INNER JOIN [ModulosSAO].[dbo].BaseDatosObra bdo ON
    upo.IDBaseDatos = bdo.IDBaseDatos
INNER JOIN [ModulosSAO].[ControlRemesas].DocumentosProcesados dp ON
	dp.IDDocumento = d.IDDocumento  and dp.IDProceso =4
INNER JOIN [ModulosSAO].[ControlRemesas].[RemesasTipoRemesa] rtr
    ON rtr.IDTipoRemesa = r.IDTipoRemesa
WHERE
    d.IDTransaccionCDC= ".$solicitud["id_transaccion"]." and
    upo.id_obra = ".$solicitud["id_obra"]." and
    bdo.BaseDatos = '".$solicitud["base_datos"]."'

) as vurdscm

        ")
            ;
            $datos_remesa = array_map(function ($value) {
                return (array)$value;
            }, $datos_remesa);

            if(key_exists(0,$datos_remesa)){

                $solicitudes[$i] = array_merge($solicitud, $datos_remesa[0]);
            }

            $i++;
        }
        return $solicitudes;
    }

    public static function getIndicadorAplicadasCompletas()
    {
        //pagos anticipados pagados / pagos anticipados registrados

        $total_solicitudes = SolicitudPagoAplicada::where("estado_registro","=",1)
            //->where("fecha_solicitud",">=",'2022-01-01 00:00:00')
            ->count();

        $total_solicitudes_con_orden_pago = SolicitudPagoAplicada::where("estado_registro","=",1)
            //->where("fecha_solicitud",">=",'2022-01-01 00:00:00')
            ->where("pendiente","<=",0.99)->count();
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
            "aplicados"=> number_format($total_solicitudes_con_orden_pago,0) . " aplicadas",
            "pendientes"=> number_format($total_solicitudes-$total_solicitudes_con_orden_pago,0) . " pendientes",
            "dividendo" => number_format($total_solicitudes,0) . " registradas",
            "color" => $color
        ];
    }




}
