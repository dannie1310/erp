<?php


namespace App\Informes\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\Reportes\CatalogoMeses;
use Illuminate\Support\Facades\DB;

class InformeSATLP
{
    public static function  get($data)
    {
        $informe["partidas"] = InformeSATLP::getInforme($data);
        $informe["empresas"] = InformeSATLP::getEmpresas();
        return $informe;
    }



    public static function getEmpresas()
    {
        $informe = DB::connection("seguridad")->select("SELECT DISTINCT
       informe_sat_lista_empresa.numero as id,
       cast(informe_sat_lista_empresa.numero as varchar(100)) + ' ' +informe_sat_lista_empresa.descripcion as label,
                cast(informe_sat_lista_empresa.numero as varchar(100)) + ' ' +informe_sat_lista_empresa.descripcion as customLabel
  FROM (SEGURIDAD_ERP.Contabilidad.ListaEmpresas ListaEmpresas
        RIGHT OUTER JOIN
        SEGURIDAD_ERP.Contabilidad.informe_sat_lista_empresa informe_sat_lista_empresa
           ON (ListaEmpresas.NumeroEmpresa = informe_sat_lista_empresa.numero))
       LEFT OUTER JOIN
       SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
          ON (cuentas_movimientos.id_empresa_contpaq =
                 informe_sat_lista_empresa.numero)");
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function  getInforme($data)
    {
        $qry = "";
        $qry_cfdi = "";
        $qry_cfdi_orden = "";
        if(count($data["empresas"])>0)
        {
            $qry = " AND IDEmpresa IN(".implode(",", $data["empresas"]).")";
        }

        $informe_qry = "SELECT proveedores_sat.IDProveedor as id_proveedor_sat,
       proveedores_sat.Descripcion as razon_social,
       proveedores_sat.RFC as rfc,
       case when movimientos_pasivo.Importe is null then '-' when movimientos_pasivo.Importe = 0 then '-'  else format(movimientos_pasivo.Importe,'C') end importe_movimientos_pasivo,

       cfdi_completos.neto_subtotal_completos,
       cfdi_completos.total_completos as neto_total_completos,
       cfdi_divisas.neto_subtotal_divisas,
       case when cfdi_divisas.total_divisas is null then '-' else format(cfdi_divisas.total_divisas,'C') end neto_total_divisas,
       case when cfdi_reemplazado.neto_subtotal_reemplazado is null then '-' else format(cfdi_reemplazado.neto_subtotal_reemplazado,'C') end neto_subtotal_reemplazado,
       case when cfdi_reemplazado.total_remplazado is null then '-' else format(cfdi_reemplazado.total_remplazado,'C') end neto_total_reemplazado,

       cfdi_reemplazo.subtotal_neto_reemplazo,

       case when cfdi_reemplazo.total_reemplazo is null then '-' else format(cfdi_reemplazo.total_reemplazo,'C') end neto_total_reemplazo,


       case when cfdi_sin_empresa.cantidad_sin_empresa is null or cfdi_sin_empresa.cantidad_sin_empresa = 0 then '-'
           else cfdi_sin_empresa.cantidad_sin_empresa end cantidad_sin_empresa,
       case when cfdi_sin_empresa.total_sin_empresa is null then '-' when cfdi_sin_empresa.total_sin_empresa = 0 then '-'
           else format(cfdi_sin_empresa.total_sin_empresa,'C') end neto_total_sin_empresa,


       case when cfdi_con_empresa.cantidad_con_empresa is null or cfdi_con_empresa.cantidad_con_empresa = 0 then '-'
           else cfdi_con_empresa.cantidad_con_empresa end cantidad_con_empresa,
       case when cfdi_con_empresa.total_con_empresa is null then '-' when cfdi_con_empresa.total_con_empresa = 0 then '-'
           else format(cfdi_con_empresa.total_con_empresa,'C') end neto_total_con_empresa,

       cfdi_i.neto_subtotal_i,
       cfdi_i.total_i as neto_total_i,

       case when cfdi_e.neto_subtotal_e is null then '-' else format(cfdi_e.neto_subtotal_e,'C') end neto_subtotal_e,
       case when cfdi_e.total_e is null then '-' else format(cfdi_e.total_e,'C') end neto_total_e,

       cfdi.neto_subtotal as neto_subtotal_sat,
       cfdi.Total as neto_total_sat,

       movimientos_pasivo.cantidad_cuentas as cantidad_cuentas,
       null as cantidad_empresas,

       cfdi.Total - movimientos_pasivo.Importe AS diferencia

  FROM ((((((((SEGURIDAD_ERP.InformeSAT.DimProveedores proveedores_sat
               LEFT OUTER JOIN
               (SELECT HecCFDISinEmpresa.IDProveedor,
               COUNT(HecCFDISinEmpresa.IDCFDI) AS cantidad_sin_empresa,
                       SUM (HecCFDISinEmpresa.SubtotalNeto)
                          AS neto_subtotal_sin_empresa,
                       SUM (HecCFDISinEmpresa.Total) AS total_sin_empresa
                  FROM SEGURIDAD_ERP.InformeSAT.HecCFDISinEmpresa HecCFDISinEmpresa
               WHERE HecCFDISinEmpresa.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'

                GROUP BY HecCFDISinEmpresa.IDProveedor) cfdi_sin_empresa
                  ON (proveedores_sat.IDProveedor = cfdi_sin_empresa.IDProveedor))
              LEFT OUTER JOIN
              (SELECT HecCFDIReemplazado.IDProveedor,
                      SUM (HecCFDIReemplazado.SubtotalNeto)
                         AS neto_subtotal_reemplazado,
                      SUM (HecCFDIReemplazado.Total) AS total_remplazado
                 FROM SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazado HecCFDIReemplazado
              WHERE HecCFDIReemplazado.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry

               GROUP BY HecCFDIReemplazado.IDProveedor) cfdi_reemplazado
                 ON (proveedores_sat.IDProveedor = cfdi_reemplazado.IDProveedor))
             LEFT OUTER JOIN
             (SELECT count(distinct HecMovimientos.Codigo) as cantidad_cuentas,HecMovimientos.IDProveedor,
                     SUM (HecMovimientos.Importe) AS Importe
                FROM SEGURIDAD_ERP.InformeSAT.HecMovimientos HecMovimientos
             WHERE HecMovimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
              GROUP BY HecMovimientos.IDProveedor) movimientos_pasivo
                ON (proveedores_sat.IDProveedor = movimientos_pasivo.IDProveedor))
            LEFT OUTER JOIN
            (SELECT HecCFDIDivisas.IDProveedor,
                    SUM (HecCFDIDivisas.SubtotalNeto)
                       AS neto_subtotal_divisas,
                    SUM (HecCFDIDivisas.Total) AS total_divisas
               FROM SEGURIDAD_ERP.InformeSAT.HecCFDIDivisas HecCFDIDivisas
            WHERE HecCFDIDivisas.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             GROUP BY HecCFDIDivisas.IDProveedor) cfdi_divisas
               ON (proveedores_sat.IDProveedor = cfdi_divisas.IDProveedor))
           LEFT OUTER JOIN
           (SELECT HecCFDII.IDProveedor,
                   SUM (HecCFDII.SubtotalNeto) AS neto_subtotal_i,
                   SUM (HecCFDII.Total) AS total_i
              FROM SEGURIDAD_ERP.InformeSAT.HecCFDII HecCFDII
           WHERE HecCFDII.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
            GROUP BY HecCFDII.IDProveedor) cfdi_i
              ON (proveedores_sat.IDProveedor = cfdi_i.IDProveedor))
          LEFT OUTER JOIN
          (SELECT HecCFDIE.IDProveedor,
                  SUM (HecCFDIE.SubtotalNeto) AS neto_subtotal_e,
                  SUM (HecCFDIE.Total) AS total_e
             FROM SEGURIDAD_ERP.InformeSAT.HecCFDIE HecCFDIE
          WHERE HecCFDIE.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
           GROUP BY HecCFDIE.IDProveedor) cfdi_e
             ON (proveedores_sat.IDProveedor = cfdi_e.IDProveedor))
         LEFT OUTER JOIN
         (SELECT HecCFDIReemplazo.IDProveedor,
                 SUM (HecCFDIReemplazo.SubtotalNeto)
                    AS subtotal_neto_reemplazo,
                 SUM (HecCFDIReemplazo.Total) AS total_reemplazo
            FROM SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazo HecCFDIReemplazo
         WHERE HecCFDIReemplazo.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
          GROUP BY HecCFDIReemplazo.IDProveedor) cfdi_reemplazo
            ON (proveedores_sat.IDProveedor = cfdi_reemplazo.IDProveedor))
        LEFT OUTER JOIN
        (SELECT HecCFDI.IDProveedor,
                SUM (HecCFDI.SubtotalNeto) AS neto_subtotal,
                SUM (HecCFDI.Total) AS total
           FROM SEGURIDAD_ERP.InformeSAT.HecCFDI HecCFDI
        WHERE HecCFDI.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
         GROUP BY HecCFDI.IDProveedor) cfdi
           ON (proveedores_sat.IDProveedor = cfdi.IDProveedor))
        LEFT JOIN
       (SELECT HecCFDICompletos.IDProveedor,
               SUM (HecCFDICompletos.SubtotalNeto) AS neto_subtotal_completos,
               SUM (HecCFDICompletos.Total) AS total_completos
          FROM SEGURIDAD_ERP.InformeSAT.HecCFDICompletos HecCFDICompletos
       WHERE HecCFDICompletos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry

        GROUP BY HecCFDICompletos.IDProveedor) cfdi_completos
          ON (proveedores_sat.IDProveedor = cfdi_completos.IDProveedor)


          LEFT OUTER JOIN
        (SELECT HecCFDI.IDProveedor,
        count(HecCFDI.IDCFDI) as cantidad_con_empresa,
                SUM (HecCFDI.SubtotalNeto) AS neto_subtotal_con_empresa,
                SUM (HecCFDI.Total) AS total_con_empresa
           FROM SEGURIDAD_ERP.InformeSAT.HecCFDI HecCFDI
           where HecCFDI.IDEmpresa is not null
             and HecCFDI.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
         GROUP BY HecCFDI.IDProveedor) cfdi_con_empresa
           ON (proveedores_sat.IDProveedor = cfdi_con_empresa.IDProveedor)

          ORDER BY cfdi.Total - movimientos_pasivo.Importe DESC
          ";


        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function getCuentas($data)
    {
        $qry = "";
        if(count($data["empresas"])>0)
        {
            $qry = " AND cuentas_movimientos.id_empresa_contpaq IN(".implode(",", $data["empresas"]).")";
        }
        $informe = DB::connection("seguridad")->select("SELECT tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat,
       cuentas_movimientos.codigo_cuenta,
       cuentas_movimientos.id_cuenta,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       SUM (cuentas_movimientos.importe_movimiento) AS importe_movimiento
  FROM (SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
        INNER JOIN
        (SELECT cuentas_movimientos.id_cuenta,
                cuentas_movimientos.codigo_cuenta
           FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
          WHERE (cuentas_movimientos.codigo_cuenta LIKE '2120%')
         UNION
         SELECT cuentas_movimientos.id_cuenta,
                cuentas_movimientos.codigo_cuenta
           FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
          WHERE (cuentas_movimientos.codigo_cuenta LIKE '2130%')
         UNION
         SELECT cuentas_movimientos.id_cuenta,
                cuentas_movimientos.codigo_cuenta
           FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
          WHERE (cuentas_movimientos.codigo_cuenta LIKE '2165%')) Subquery
           ON (cuentas_movimientos.id_cuenta = Subquery.id_cuenta))
       INNER JOIN
       SEGURIDAD_ERP.Contabilidad.tmp_cuentas_contpaq_proveedores_sat tmp_cuentas_contpaq_proveedores_sat
          ON (tmp_cuentas_contpaq_proveedores_sat.id_cuenta =
                 cuentas_movimientos.id_cuenta)
LEFT JOIN
       SEGURIDAD_ERP.Contabilidad.informe_sat_lista_empresa informe_sat_lista_empresa
          ON (informe_sat_lista_empresa.numero =
                 cuentas_movimientos.id_empresa_contpaq)

 WHERE     (tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat = ".$data["id"].")
       AND (cuentas_movimientos.tipo_poliza in(3,2))
       AND (cuentas_movimientos.tipo_movimiento = 'VERDADERO')
        AND cuentas_movimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                                    AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
                                                    $qry
GROUP BY tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat,
cuentas_movimientos.id_cuenta,
informe_sat_lista_empresa.descripcion,
         cuentas_movimientos.codigo_cuenta
         order by SUM (cuentas_movimientos.importe_movimiento) desc");

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function getMovimientos($data)
    {
        $qry = "";
        if(count($data["empresas"])>0)
        {
            $qry = " AND cuentas_movimientos.id_empresa_contpaq IN(".implode(",", $data["empresas"]).")";
        }
        $informe = DB::connection("seguridad")->select("SELECT distinct tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat,
       cuentas_movimientos.codigo_cuenta,
       CONVERT(varchar,cuentas_movimientos.fecha,103)  as fecha_poliza,
       cuentas_movimientos.folio_poliza,
       case cuentas_movimientos.tipo_poliza when 1 then 'Ingresos' when 2 then 'Egresos' when 3 then 'Diario' end tipo_poliza,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       cuentas_movimientos.importe_movimiento AS importe_movimiento,
       cuentas_movimientos.id_poliza,
       40 as id_empresa
  FROM (SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
        INNER JOIN
        (SELECT cuentas_movimientos.id_cuenta,
                cuentas_movimientos.codigo_cuenta
           FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
          WHERE (cuentas_movimientos.codigo_cuenta LIKE '2120%')
         UNION
         SELECT cuentas_movimientos.id_cuenta,
                cuentas_movimientos.codigo_cuenta
           FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
          WHERE (cuentas_movimientos.codigo_cuenta LIKE '2130%')
         UNION
         SELECT cuentas_movimientos.id_cuenta,
                cuentas_movimientos.codigo_cuenta
           FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
          WHERE (cuentas_movimientos.codigo_cuenta LIKE '2165%')) Subquery
           ON (cuentas_movimientos.id_cuenta = Subquery.id_cuenta))
       INNER JOIN
       SEGURIDAD_ERP.Contabilidad.tmp_cuentas_contpaq_proveedores_sat tmp_cuentas_contpaq_proveedores_sat
          ON (tmp_cuentas_contpaq_proveedores_sat.id_cuenta =
                 cuentas_movimientos.id_cuenta)
LEFT JOIN
       SEGURIDAD_ERP.Contabilidad.informe_sat_lista_empresa informe_sat_lista_empresa
          ON (informe_sat_lista_empresa.numero =
                 cuentas_movimientos.id_empresa_contpaq)

LEFT JOIN
       SEGURIDAD_ERP.Contabilidad.ListaEmpresas ListaEmpresas
          ON (ListaEmpresas.NumeroEmpresa =
                 cuentas_movimientos.id_empresa_contpaq and ListaEmpresas.AliasBDD like '%ctPCO811231EI4_%')


 WHERE     ( cuentas_movimientos.id_cuenta = ".$data["id_cuenta"].")
       AND (cuentas_movimientos.tipo_poliza  in(3,2) )
       AND (cuentas_movimientos.tipo_movimiento = 'VERDADERO')
       AND cuentas_movimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
        $qry
order by cuentas_movimientos.importe_movimiento desc");

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function getListaCFDI($id_proveedor, $fecha_inicial, $fecha_final, $asociada_contpaq, $empresas){

        $condicion = " AND cfd_sat.numero_empresa is null";
        if(!$asociada_contpaq && count($empresas) == 0){
            $condicion = "";
        } else if(!$asociada_contpaq && count($empresas) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $empresas).")";
        }
        if($asociada_contpaq == 1){
            if(count($empresas)>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $empresas).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      SELECT distinct cfd_sat.*,
       cfd_sat.cfdi_relacionado,
       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,
       cfd_sat.fecha_pago,
       cfd_sat.moneda_xls,
       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq
  FROM (((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
          LEFT OUTER JOIN
          SEGURIDAD_ERP.Finanzas.repositorio_facturas repositorio_facturas
             ON (cfd_sat.uuid = repositorio_facturas.uuid))
         LEFT OUTER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_1
            ON (cfd_sat.cfdi_relacionado = cfd_sat_1.uuid and cfd_sat.tipo_relacion = 4))
        LEFT OUTER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_2
           ON (cfd_sat.uuid = cfd_sat_2.cfdi_relacionado and cfd_sat_2.tipo_relacion = 4 ))
       LEFT OUTER JOIN
       SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
          ON     (repositorio_facturas.id_proyecto =
                     configuracion_obra.id_proyecto)
             AND (repositorio_facturas.id_obra = configuracion_obra.id_obra)
      LEFT OUTER JOIN
      SEGURIDAD_ERP.Contabilidad.polizas_cfdi on(polizas_cfdi.uuid = cfd_sat.uuid)
      LEFT OUTER JOIN
      SEGURIDAD_ERP.Contabilidad.informe_sat_lista_empresa on(informe_sat_lista_empresa.numero = polizas_cfdi.numero_empresa)

where cfd_sat.fecha BETWEEN '".$fecha_inicial->format("Y-m-d")." 00:00:00'
      AND '".$fecha_final->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$id_proveedor."
      AND cfd_sat.id_empresa_sat = 1
      ".$condicion."
      order by cfd_sat.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }


    public static function getListaCFDIOmitidos($id_proveedor, $fecha_inicial, $fecha_final, $asociada_contpaq, $empresas){

        $condicion = " AND cfd_sat.numero_empresa is null";
        if(!$asociada_contpaq && count($empresas) == 0){
            $condicion = "";
        } else if(!$asociada_contpaq && count($empresas) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $empresas).")";
        }
        if($asociada_contpaq == 1){
            if(count($empresas)>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $empresas).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      SELECT cfd_sat.*,
       cfd_sat.cfdi_relacionado,
       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,
       cfd_sat.fecha_pago,
       cfd_sat.moneda_xls,
       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq
  FROM (((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
          LEFT OUTER JOIN
          SEGURIDAD_ERP.Finanzas.repositorio_facturas repositorio_facturas
             ON (cfd_sat.uuid = repositorio_facturas.uuid))
         LEFT OUTER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_1
            ON (cfd_sat.cfdi_relacionado = cfd_sat_1.uuid and cfd_sat.tipo_relacion = 4))
        LEFT OUTER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_2
           ON (cfd_sat.uuid = cfd_sat_2.cfdi_relacionado and cfd_sat_2.tipo_relacion = 4 ))
       LEFT OUTER JOIN
       SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
          ON     (repositorio_facturas.id_proyecto =
                     configuracion_obra.id_proyecto)
             AND (repositorio_facturas.id_obra = configuracion_obra.id_obra)
      LEFT OUTER JOIN
      SEGURIDAD_ERP.Contabilidad.polizas_cfdi on(polizas_cfdi.uuid = cfd_sat.uuid)
      LEFT OUTER JOIN
      SEGURIDAD_ERP.Contabilidad.informe_sat_lista_empresa on(informe_sat_lista_empresa.numero = polizas_cfdi.numero_empresa)

where cfd_sat.fecha BETWEEN '".$fecha_inicial->format("Y-m-d")." 00:00:00'
      AND '".$fecha_final->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$id_proveedor."
      AND cfd_sat.id_empresa_sat = 1
      ".$condicion."
      order by cfd_sat.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

}
