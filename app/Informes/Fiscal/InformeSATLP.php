<?php


namespace App\Informes\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\Reportes\CatalogoMeses;
use Illuminate\Support\Facades\DB;

class InformeSATLP
{
    public static function  get($data)
    {
        $informe["partidas"] = InformeSATLP::getInforme($data);
        $informe["empresas"] = InformeSATLP::getEmpresas($data);
        $informe["empresas_sat"] = InformeSATLP::getEmpresasSAT();
        $informe["sin_proveedor"] = InformeSATLP::getMovimientosSinProveedor($data);
        $informe["rango_fechas"] = InformeSATLP::getRangoFechas($data);
        $informe["empresa"] = InformeSATLP::getEmpresa($data);
        return $informe;
    }

    public static function getEmpresa($data)
    {
        return EmpresaSAT::find($data["empresa_sat"])->razon_social;
    }

    public static function getRangoFechas($data)
    {
        $query = "select min(Fecha) as fecha_inicial, max(Fecha) as fecha_final
from SEGURIDAD_ERP.InformeSAT.HecCFDICompletos where IDEmpresaSAT = ".$data["empresa_sat"];
        $fechas = DB::connection("seguridad")->select($query);
        $fechas = array_map(function ($value) {
            return (array)$value;
        }, $fechas);

        return $fechas;
    }

    public static function getEmpresas($data)
    {
        $informe = DB::connection("seguridad")->select("SELECT
    dec.IDEmpresaContpaq as id,
    cast(dec.Numero as varchar(100)) + ' ' + dec.Descripcion as label,
    cast(dec.Numero as varchar(100)) + ' ' + dec.Descripcion as customLabelr
FROM
    SEGURIDAD_ERP.InformeSAT.DimEmpresasContpaq dec
WHERE IDEmpresaSAT = ".$data["empresa_sat"]." or IDEmpresaSAT is null
ORDER BY
    dec.Numero;");
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function getEmpresasSAT()
    {
        $informe = DB::connection("seguridad")->select("SELECT
    dec.IDEmpresaSAT as id,
    dec.Descripcion as label,
    dec.Descripcion as customLabel,
       min(hc.Fecha) as fecha_inicial,
       max(hc.Fecha) as fecha_final
FROM
    SEGURIDAD_ERP.InformeSAT.DimEmpresas dec join SEGURIDAD_ERP.InformeSAT.HecCFDICompletos as hc
on hc.IDEmpresaSAT = dec.IDEmpresaSAT group by dec.IDEmpresaSAT, dec.Descripcion
ORDER BY
    dec.Descripcion;");
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return collect($informe);
    }

    public static function getMovimientosSinProveedor($data)
    {
        $qry = "";
        $qry2132 = "";
        if(count($data["empresas"])>0)
        {
            $qry = " AND (IDEmpresa IN(".implode(",", $data["empresas"]).") OR IDEmpresa is null) ";
        }

        if($data["con2132"] == 0)
        {
            $qry2132 = " AND IDCuentaAgrupador IN(1,2,3,5,6,7,8,10,11,12) ";
        }
        //AND HecCFDISinEmpresa.IDEmpresaSAT = ".$data["empresa_sat"]."

        $informe_qry = "SELECT proveedores_sat.IDProveedor as id_proveedor_sat,
       proveedores_sat.Descripcion as razon_social,
       proveedores_sat.RFC as rfc,
       case when movimientos_pasivo.Importe is null then '-' when movimientos_pasivo.Importe = 0 then '-'  else format(movimientos_pasivo.Importe,'C') end importe_movimientos_pasivo,
       '-' as movimientos_pasivo_importe,
      '-',
       '-' as neto_total_completos,
       '-',
       '-' as neto_total_divisas,
       '-' as neto_subtotal_reemplazado,
       '-' as neto_total_reemplazado,
       '-' as neto_total_dispersion,
       '-' ,

       '-' as neto_total_reemplazo,


       '-' as cantidad_sin_empresa,
       '-' as neto_total_sin_empresa,


       '-' as cantidad_con_empresa,
       '-' as neto_total_con_empresa,

       '-' ,
       '-' as neto_total_i,

       '-' as neto_subtotal_e,
       '-' as neto_total_e,

       0 as neto_subtotal_sat,
       0 as neto_total_sat,

       movimientos_pasivo.cantidad_cuentas as cantidad_cuentas,
       null as cantidad_empresas,

       format(0 - isnull(movimientos_pasivo.Importe,0), 'C') AS diferencia,

       '-' as neto_subtotal_no_cancelados,
       '-' as neto_total_no_cancelados,

       '-' as neto_total_agregar,

      '-' as neto_total_cancelados

  FROM SEGURIDAD_ERP.InformeSAT.DimProveedores proveedores_sat

             LEFT OUTER JOIN
             (SELECT count(distinct HecMovimientos.Codigo) as cantidad_cuentas,HecMovimientos.IDProveedor,
                     SUM (HecMovimientos.Importe) AS Importe
                FROM SEGURIDAD_ERP.InformeSAT.HecMovimientos HecMovimientos
             WHERE HecMovimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry $qry2132
             AND HecMovimientos.IDEmpresaSAT = ".$data["empresa_sat"]."
              GROUP BY HecMovimientos.IDProveedor) movimientos_pasivo
                ON (proveedores_sat.IDProveedor = movimientos_pasivo.IDProveedor)

                where proveedores_sat.Descripcion ='SIN PROVEEDOR'

          ORDER BY 0 - isnull(movimientos_pasivo.Importe,0) DESC
          ";


        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function getInforme($data)
    {
        $qry = "";
        $qry2132 = "";
        if(count($data["empresas"])>0)
        {
            $qry = " AND (IDEmpresa IN(".implode(",", $data["empresas"]).") OR IDEmpresa is null) ";
        }

        if($data["con2132"] == 0)
        {
            $qry2132 = " AND IDCuentaAgrupador IN(1,2,3,5,6,7,8,10,11,12) ";
        }
        //AND HecCFDISinEmpresa.IDEmpresaSAT = ".$data["empresa_sat"]."

        $informe_qry = "SELECT proveedores_sat.IDProveedor as id_proveedor_sat,
       proveedores_sat.Descripcion as razon_social,
       proveedores_sat.RFC as rfc,
       case when movimientos_pasivo.Importe is null then '-' when movimientos_pasivo.Importe = 0 then '-'  else format(movimientos_pasivo.Importe,'C') end importe_movimientos_pasivo,
       movimientos_pasivo.Importe as movimientos_pasivo_importe,
       cfdi_completos.neto_subtotal_completos,
       cfdi_completos.total_completos as neto_total_completos,
       cfdi_divisas.neto_subtotal_divisas,
       case when cfdi_divisas.total_divisas is null then '-' else format(cfdi_divisas.total_divisas,'C') end neto_total_divisas,
       case when cfdi_reemplazado.neto_subtotal_reemplazado is null then '-' else format(cfdi_reemplazado.neto_subtotal_reemplazado,'C') end neto_subtotal_reemplazado,
       case when cfdi_reemplazado.total_remplazado is null then '-' else format(cfdi_reemplazado.total_remplazado,'C') end neto_total_reemplazado,
       case when cfdi_dispersion.total_dispersion is null then '-' else format(cfdi_dispersion.total_dispersion,'C') end neto_total_dispersion,
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

       format(isnull(cfdi.Total,0) - isnull(movimientos_pasivo.Importe,0), 'C') AS diferencia,

       case when cfdi_reemplazados_no_cancelados.subtotal_neto is null then '-' else format(cfdi_reemplazados_no_cancelados.subtotal_neto,'C') end neto_subtotal_no_cancelados,
       case when cfdi_reemplazados_no_cancelados.total_neto is null then '-' else format(cfdi_reemplazados_no_cancelados.total_neto,'C') end neto_total_no_cancelados,

       case when cfdi_reemplazados.total_neto is null then '-' else format(cfdi_reemplazados.total_neto,'C') end neto_total_agregar,

       case when cfdi_cancelados.neto_total_cancelados is null then '-' else format(cfdi_cancelados.neto_total_cancelados,'C') end neto_total_cancelados

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
              AND HecCFDISinEmpresa.IDEmpresaSAT = ".$data["empresa_sat"]."

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
             AND HecCFDIReemplazado.IDEmpresaSAT = ".$data["empresa_sat"]."

               GROUP BY HecCFDIReemplazado.IDProveedor) cfdi_reemplazado
                 ON (proveedores_sat.IDProveedor = cfdi_reemplazado.IDProveedor))
             LEFT OUTER JOIN
             (SELECT count(distinct HecMovimientos.Codigo) as cantidad_cuentas,HecMovimientos.IDProveedor,
                     SUM (HecMovimientos.Importe) AS Importe
                FROM SEGURIDAD_ERP.InformeSAT.HecMovimientos HecMovimientos
             WHERE HecMovimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry $qry2132
             AND HecMovimientos.IDEmpresaSAT = ".$data["empresa_sat"]."
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
             AND HecCFDIDivisas.IDEmpresaSAT = ".$data["empresa_sat"]."
             GROUP BY HecCFDIDivisas.IDProveedor) cfdi_divisas
               ON (proveedores_sat.IDProveedor = cfdi_divisas.IDProveedor))
           LEFT OUTER JOIN
           (SELECT HecCFDII.IDProveedor,
                   SUM (HecCFDII.SubtotalNeto) AS neto_subtotal_i,
                   SUM (HecCFDII.Total) AS total_i
              FROM SEGURIDAD_ERP.InformeSAT.HecCFDII HecCFDII
           WHERE HecCFDII.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDII.IDEmpresaSAT = ".$data["empresa_sat"]."
            GROUP BY HecCFDII.IDProveedor) cfdi_i
              ON (proveedores_sat.IDProveedor = cfdi_i.IDProveedor))
          LEFT OUTER JOIN
          (SELECT HecCFDIE.IDProveedor,
                  SUM (HecCFDIE.SubtotalNeto) AS neto_subtotal_e,
                  SUM (HecCFDIE.Total) AS total_e
             FROM SEGURIDAD_ERP.InformeSAT.HecCFDIE HecCFDIE
          WHERE HecCFDIE.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDIE.IDEmpresaSAT = ".$data["empresa_sat"]."
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
             AND HecCFDIReemplazo.IDEmpresaSAT = ".$data["empresa_sat"]."
          GROUP BY HecCFDIReemplazo.IDProveedor) cfdi_reemplazo
            ON (proveedores_sat.IDProveedor = cfdi_reemplazo.IDProveedor))
        LEFT OUTER JOIN
        (SELECT HecCFDI.IDProveedor,
                SUM (HecCFDI.SubtotalNeto) AS neto_subtotal,
                SUM (HecCFDI.Total) AS total
           FROM SEGURIDAD_ERP.InformeSAT.HecCFDI HecCFDI
        WHERE HecCFDI.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDI.IDEmpresaSAT = ".$data["empresa_sat"]."
         GROUP BY HecCFDI.IDProveedor) cfdi
           ON (proveedores_sat.IDProveedor = cfdi.IDProveedor))
        JOIN
       (SELECT HecCFDICompletos.IDProveedor,
               SUM (HecCFDICompletos.SubtotalNeto) AS neto_subtotal_completos,
               SUM (HecCFDICompletos.Total) AS total_completos
          FROM SEGURIDAD_ERP.InformeSAT.HecCFDICompletos HecCFDICompletos
       WHERE HecCFDICompletos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDICompletos.IDEmpresaSAT = ".$data["empresa_sat"]."
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
             AND HecCFDI.IDEmpresaSAT = ".$data["empresa_sat"]."
         GROUP BY HecCFDI.IDProveedor) cfdi_con_empresa
           ON (proveedores_sat.IDProveedor = cfdi_con_empresa.IDProveedor)

           LEFT OUTER JOIN
         (SELECT HecCFDIDispersion.IDProveedor,
                 SUM (HecCFDIDispersion.SubtotalNeto)
                    AS subtotal_neto_dispersion,
                 SUM (HecCFDIDispersion.Total) AS total_dispersion
            FROM SEGURIDAD_ERP.InformeSAT.HecCFDIDispersion HecCFDIDispersion
         WHERE HecCFDIDispersion.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDIDispersion.IDEmpresaSAT = ".$data["empresa_sat"]."
          GROUP BY HecCFDIDispersion.IDProveedor) cfdi_dispersion
            ON (proveedores_sat.IDProveedor = cfdi_dispersion.IDProveedor)

            LEFT OUTER JOIN
         (SELECT HecCFDIReemplazadosNoCancelados.IDProveedor,
                 SUM (HecCFDIReemplazadosNoCancelados.SubtotalNeto)
                    AS subtotal_neto,
                 SUM (HecCFDIReemplazadosNoCancelados.Total) AS total_neto
            FROM SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazadosNoCancelados HecCFDIReemplazadosNoCancelados
         WHERE HecCFDIReemplazadosNoCancelados.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDIReemplazadosNoCancelados.IDEmpresaSAT = ".$data["empresa_sat"]."
          GROUP BY HecCFDIReemplazadosNoCancelados.IDProveedor) cfdi_reemplazados_no_cancelados
            ON (proveedores_sat.IDProveedor = cfdi_reemplazados_no_cancelados.IDProveedor)

             LEFT OUTER JOIN
         (SELECT HecCFDIReemplazado.IDProveedor,
                 SUM (HecCFDIReemplazado.SubtotalNeto)
                    AS subtotal_neto,
                 SUM (HecCFDIReemplazado.Total) AS total_neto
            FROM SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazado HecCFDIReemplazado
         WHERE HecCFDIReemplazado.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDIReemplazado.IDEmpresaSAT = ".$data["empresa_sat"]."
          GROUP BY HecCFDIReemplazado.IDProveedor) cfdi_reemplazados
            ON (proveedores_sat.IDProveedor = cfdi_reemplazados.IDProveedor)

         LEFT JOIN
       (SELECT HecCFDICancelados.IDProveedor,
               SUM (HecCFDICancelados.SubtotalNeto) AS neto_subtotal_cancelados,
               SUM (HecCFDICancelados.Total) AS neto_total_cancelados
          FROM SEGURIDAD_ERP.InformeSAT.HecCFDICancelados HecCFDICancelados
       WHERE HecCFDICancelados.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
             AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry
             AND HecCFDICancelados.IDEmpresaSAT = ".$data["empresa_sat"]."

        GROUP BY HecCFDICancelados.IDProveedor) cfdi_cancelados
          ON (proveedores_sat.IDProveedor = cfdi_cancelados.IDProveedor)

          ORDER BY isnull(cfdi.Total,0) - isnull(movimientos_pasivo.Importe,0) DESC
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
        $qry2132 = "";
        if(count($data["empresas"])>0)
        {
            $qry = " AND hm.IDEmpresa IN(".implode(",", $data["empresas"]).")";
        }

        if($data["con2132"] == 0)
        {
            $qry2132 = " AND IDCuentaAgrupador IN(1,2,3,5,6,7,8,10,11,12) ";
        }

        $query = "SELECT
    dc.IDProveedor AS id_proveedor_sat,
    dc.Descripcion AS codigo_cuenta,
    dc.Nombre AS nombre_cuenta,
    dc.IdCuenta AS id_cuenta,
    dec.Descripcion AS empresa_contpaq,
    SUM(hm.Importe) AS importe_movimiento
FROM
    SEGURIDAD_ERP.InformeSAT.DimCuentas dc
INNER JOIN SEGURIDAD_ERP.InformeSAT.HecMovimientos hm ON
    hm.IDCuenta = dc.IdCuenta
INNER JOIN SEGURIDAD_ERP.InformeSAT.DimEmpresasContpaq dec ON
    hm.IDEmpresa = dec.IDEmpresaContpaq
WHERE dc.IDProveedor = ".$data["id"]."
AND hm.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59' $qry $qry2132
AND hm.IDEmpresaSAT = ".$data["empresa_sat"]."
GROUP BY
    dc.IDProveedor,
    dc.Descripcion,
    dc.Nombre,
    dc.IdCuenta,
    dec.Descripcion order by SUM(hm.Importe) desc";

        $informe = DB::connection("seguridad")->select($query);

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
            $qry = " AND hm.IDEmpresa IN(".implode(",", $data["empresas"]).")";
        }

        $query = "SELECT
   hm.IDProveedor as id_proveedor_sat,
    hm.Codigo as codigo_cuenta,
    hm.Fecha as fecha_poliza,
    hm.FolioPoliza as folio_poliza,
    hm.TipoPoliza as tipo_poliza,
    dec.Descripcion as empresa_contpaq,
    hm.Importe as importe_movimiento,
    hm.IDPoliza as id_poliza,
    hm.IDEmpresa as id_empresa,
    die.IDEmpresa as id_empresa_consolidadora
FROM
    SEGURIDAD_ERP.InformeSAT.HecMovimientos hm
INNER JOIN SEGURIDAD_ERP.InformeSAT.DimEmpresasContpaq dec ON
    hm.IDEmpresa = dec.IDEmpresaContpaq
INNER JOIN SEGURIDAD_ERP.InformeSAT.DimEmpresas die ON
    hm.IDEmpresaSAT = die.IDEmpresaSAT
 WHERE   hm.IDCuenta = ".$data["id_cuenta"]."
       AND hm.Fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
        $qry
        AND hm.IDEmpresaSAT = ".$data["empresa_sat"]."
order by hm.Importe desc
";

        $informe = DB::connection("seguridad")->select($query);

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;

    }

    public static function getListaCFDI($data){


        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDICompletos.Total as total_a_sumar
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
 JOIN SEGURIDAD_ERP.InformeSAT.HecCFDICompletos on(HecCFDICompletos.IDCFDI = cfd_sat.id)
where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDICompletos.Total as total_a_sumar
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
JOIN SEGURIDAD_ERP.InformeSAT.HecCFDICompletos on(HecCFDICompletos.IDCFDI = cfd_sat.id)
where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];
    }

    public static function getListaCFDIOmitidosDivisa($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDIDivisas.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIDivisas on(HecCFDIDivisas.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDIDivisas.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIDivisas on(HecCFDIDivisas.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIOmitidosReemplazo($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDIReemplazo.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazo on(HecCFDIReemplazo.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDIReemplazo.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazo on(HecCFDIReemplazo.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIReemplazados($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDIReemplazado.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazado on(HecCFDIReemplazado.IDCFDI = cfd_sat.id)


where
      cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDIReemplazado.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazado on(HecCFDIReemplazado.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIIngresos($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDII.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDII on(HecCFDII.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDII.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDII on(HecCFDII.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIEgresos($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDIE.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIE on(HecCFDIE.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDIE.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIE on(HecCFDIE.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIReconocidos($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDI.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDI on(HecCFDI.IDCFDI = cfd_sat.id and cfd_sat.numero_empresa is not null)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDI.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDI on(HecCFDI.IDCFDI = cfd_sat.id and cfd_sat.numero_empresa is not null)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDINoReconocidos($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDISinEmpresa.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDISinEmpresa on(HecCFDISinEmpresa.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDISinEmpresa.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDISinEmpresa on(HecCFDISinEmpresa.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIARevisar($data)
    {
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDI.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDI on(HecCFDI.IDCFDI = cfd_sat.id)


where HecCFDI.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDI.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDI on(HecCFDI.IDCFDI = cfd_sat.id)

where HecCFDI.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIOmitidosDispersion($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
                      HecCFDIDispersion.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIDispersion on(HecCFDIDispersion.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDIDispersion.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIDispersion on(HecCFDIDispersion.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDIReemplazadosNoCancelados($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDIReemplazadosNoCancelados.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazadosNoCancelados on(HecCFDIReemplazadosNoCancelados.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDIReemplazadosNoCancelados.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDIReemplazadosNoCancelados on(HecCFDIReemplazadosNoCancelados.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

    public static function getListaCFDICancelados($data){
        $condicion = "";
        if(!$data["asociada_contpaq"] && count($data["empresas"]) == 0){
            $condicion = "";
        } else if(!$data["asociada_contpaq"] && count($data["empresas"]) > 0){
            $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
        }
        if($data["asociada_contpaq"] == 1){
            if(count($data["empresas"])>0)
            {
                $condicion = " AND cfd_sat.numero_empresa in(".implode(",", $data["empresas"]).")";
            } else{
                $condicion = " AND cfd_sat.numero_empresa is not null";
            }
        }

        $informe = DB::connection("seguridad")->select("
      select * from (
      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDICancelados.Total as total_a_sumar
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
      JOIN SEGURIDAD_ERP.InformeSAT.HecCFDICancelados on(HecCFDICancelados.IDCFDI = cfd_sat.id)


where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'

      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      ".$condicion."

      union

      SELECT distinct cfd_sat.*,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

       cfd_sat_2.id AS id_reemplaza,
       cfd_sat_2.fecha AS fecha_reemplaza,
       cfd_sat_2.serie AS serie_reemplaza,
       cfd_sat_2.folio AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
       HecCFDICancelados.Total as total_a_sumar
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
    JOIN SEGURIDAD_ERP.InformeSAT.HecCFDICancelados on(HecCFDICancelados.IDCFDI = cfd_sat.id)

where cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
      AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'

      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$data["id_proveedor_sat"]."
      AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
      AND cfd_sat.numero_empresa is null) as lista_cfdi

      order by lista_cfdi.fecha
 " );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        foreach($informe as $partida_informe)
        {
            $total += $partida_informe["total_a_sumar"];
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];

    }

}
