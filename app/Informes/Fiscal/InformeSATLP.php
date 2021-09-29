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
        return $informe;
    }

    public static function getCuentas($data)
    {
        $informe = DB::connection("seguridad")->select("SELECT tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat,
       cuentas_movimientos.codigo_cuenta,
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
 WHERE     (tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat = ".$data["id"].")
       AND (cuentas_movimientos.tipo_poliza = 3)
       AND (cuentas_movimientos.tipo_movimiento = 'VERDADERO')
        AND cuentas_movimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                                    AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59'
GROUP BY tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat,
         cuentas_movimientos.codigo_cuenta");

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function  getInforme($data)
    {
        $informe = DB::connection("seguridad")->select("SELECT tmp_informe_sat.id_proveedor_sat,
       proveedores_sat.razon_social,
       proveedores_sat.rfc,
       tmp_informe_sat.importe_movimientos_pasivo,
       tmp_informe_sat.neto_subtotal_i,
       tmp_informe_sat.neto_total_i,
       tmp_informe_sat.neto_subtotal_e,
       tmp_informe_sat.neto_total_e,
       tmp_informe_sat.neto_total_sat,
       tmp_informe_sat.neto_subtotal_sat,
       tmp_informe_sat.diferencia,
       tmp_informe_sat.cantidad_cuentas
  FROM SEGURIDAD_ERP.Contabilidad.proveedores_sat proveedores_sat
       INNER JOIN SEGURIDAD_ERP.Contabilidad.tmp_informe_sat tmp_informe_sat
          ON (proveedores_sat.id = tmp_informe_sat.id_proveedor_sat)
order by diferencia desc"
        )
        ;
        $informe = DB::connection("seguridad")->select("
select * from (
SELECT proveedores_sat.id as id_proveedor_sat,
       proveedores_sat.rfc,
       proveedores_sat.razon_social,
       SUM (Subquery.importe_movimiento) AS importe_movimientos_pasivo,
       Subquery_2.neto_subtotal_i,
       Subquery_2.neto_total_i,
       Subquery_3.neto_subtotal_e,
       Subquery_3.neto_total_e,
       Subquery_1.neto_subtotal AS neto_subtotal_sat,
       Subquery_1.total AS neto_total_sat,
       COUNT (DISTINCT Subquery.id_cuenta) AS cantidad_cuentas,
       Subquery_1.total - sum (ISNULL (Subquery.importe_movimiento, 0))
          AS diferencia
  FROM ((((SEGURIDAD_ERP.Contabilidad.proveedores_sat proveedores_sat
           INNER JOIN
           (SELECT proveedores_sat.id,
                   proveedores_sat.razon_social,
                   proveedores_sat.rfc,
                   SUM (Subquery.neto_subtotal) AS neto_subtotal,
                   SUM (Subquery.total) AS total
              FROM SEGURIDAD_ERP.Contabilidad.proveedores_sat proveedores_sat
                   INNER JOIN
                   (SELECT cfd_sat.id_proveedor_sat,
                           cfd_sat.subtotal_xls AS subtotal,
                           cfd_sat.descuento_xls AS descuento,
                           CASE
                              WHEN     cfd_sat.moneda_xls != 'MXN'
                                   AND cfd_sat.tc_xls > 0
                              THEN
                                 CASE
                                    WHEN cfd_sat.tipo_comprobante = 'E'
                                    THEN
                                         (  cfd_sat.subtotal_xls
                                          - cfd_sat.descuento_xls)
                                       * (-1)
                                       * cfd_sat.tc_xls
                                    WHEN cfd_sat.tipo_comprobante = 'I'
                                    THEN
                                         cfd_sat.subtotal_xls
                                       -   cfd_sat.descuento_xls
                                         * cfd_sat.tc_xls
                                 END
                              ELSE
                                 CASE
                                    WHEN cfd_sat.tipo_comprobante = 'E'
                                    THEN
                                         (  cfd_sat.subtotal_xls
                                          - cfd_sat.descuento_xls)
                                       * (-1)
                                    WHEN cfd_sat.tipo_comprobante = 'I'
                                    THEN
                                         cfd_sat.subtotal_xls
                                       - cfd_sat.descuento_xls
                                 END
                           END
                              AS neto_subtotal,
                           cfd_sat.tipo_comprobante,
                           CASE
                              WHEN     cfd_sat.moneda_xls != 'MXN'
                                   AND cfd_sat.tc_xls > 0
                              THEN
                                 CASE
                                    WHEN cfd_sat.tipo_comprobante = 'E'
                                    THEN
                                       total_xls * (-1) * cfd_sat.tc_xls
                                    WHEN cfd_sat.tipo_comprobante = 'I'
                                    THEN
                                       total_xls * cfd_sat.tc_xls
                                 END
                              ELSE
                                 CASE
                                    WHEN cfd_sat.tipo_comprobante = 'E'
                                    THEN
                                       cfd_sat.total_xls * (-1)
                                    WHEN cfd_sat.tipo_comprobante = 'I'
                                    THEN
                                       cfd_sat.total_xls
                                 END
                           END
                              AS total,
                           cfd_sat.moneda_xls,
                           cfd_sat.tc_xls
                      FROM SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                     WHERE     (cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                                  AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59')
                           AND (    (    (    cfd_sat.cancelado = 0
                                          AND cfd_sat.id_empresa_sat = 1)
                                     AND cfd_sat.tipo_comprobante IN
                                            ('E', 'I'))
                                AND year (cfd_sat.fecha) = 2020)) Subquery
                      ON (proveedores_sat.id = Subquery.id_proveedor_sat)
            GROUP BY proveedores_sat.id,
                     proveedores_sat.razon_social,
                     proveedores_sat.rfc) Subquery_1
              ON (proveedores_sat.id = Subquery_1.id))
          LEFT OUTER JOIN
          SEGURIDAD_ERP.Contabilidad.tmp_cuentas_contpaq_proveedores_sat tmp_cuentas_contpaq_proveedores_sat
             ON (tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat =
                    proveedores_sat.id))
         LEFT OUTER JOIN
         (SELECT cuentas_movimientos.periodo,
                 cuentas_movimientos.id_cuenta,
                 cuentas_movimientos.id_poliza,
                 cuentas_movimientos.codigo_cuenta,
                 cuentas_movimientos.tipo_movimiento,
                 cuentas_movimientos.nombre_cuenta,
                 cuentas_movimientos.importe_movimiento,
                 cuentas_movimientos.tipo_poliza,
                 cuentas_movimientos.id_movimiento,
                 cuentas_movimientos.folio_poliza,
                 cuentas_movimientos.fecha
            FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
                 INNER JOIN
                 (SELECT cuentas_movimientos.id_cuenta,
                         cuentas_movimientos.codigo_cuenta,
                         cuentas_movimientos.nombre_cuenta
                    FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
                   WHERE     (cuentas_movimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                                            AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59')
                         AND (   cuentas_movimientos.codigo_cuenta LIKE
                                    '2120%'
                              OR cuentas_movimientos.codigo_cuenta LIKE
                                    '2130%')
                  UNION
                  SELECT cuentas_movimientos.id_cuenta,
                         cuentas_movimientos.codigo_cuenta,
                         cuentas_movimientos.nombre_cuenta
                    FROM SEGURIDAD_ERP.Contabilidad.cuentas_movimientos cuentas_movimientos
                   WHERE     (cuentas_movimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                                            AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59')
                         AND cuentas_movimientos.codigo_cuenta LIKE '2165%')
                 Subquery
                    ON (cuentas_movimientos.id_cuenta = Subquery.id_cuenta)
           WHERE     (cuentas_movimientos.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                                    AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59')
                 AND (    (cuentas_movimientos.tipo_movimiento = 'VERDADERO')
                      AND cuentas_movimientos.tipo_poliza = 3)) Subquery
            ON (Subquery.id_cuenta =
                   tmp_cuentas_contpaq_proveedores_sat.id_cuenta))
        FULL OUTER JOIN
        (SELECT proveedores_sat.id,
                proveedores_sat.rfc,
                proveedores_sat.razon_social,
                SUM (Subquery.neto_subtotal) AS neto_subtotal_e,
                SUM (Subquery.total) AS neto_total_e
           FROM (SELECT cfd_sat.id_proveedor_sat,
                        cfd_sat.subtotal_xls AS subtotal,
                        cfd_sat.descuento_xls AS descuento,
                        CASE
                           WHEN     (cfd_sat.moneda_xls <> 'MXN')
                                AND (cfd_sat.tc_xls > 0)
                           THEN
                              CASE
                                 WHEN cfd_sat.tipo_comprobante = 'E'
                                 THEN
                                      (  (  cfd_sat.subtotal_xls
                                          - cfd_sat.descuento_xls)
                                       * (-1))
                                    * cfd_sat.tc_xls
                                 WHEN cfd_sat.tipo_comprobante = 'I'
                                 THEN
                                      cfd_sat.subtotal_xls
                                    - (cfd_sat.descuento_xls * cfd_sat.tc_xls)
                              END
                           ELSE
                              CASE
                                 WHEN cfd_sat.tipo_comprobante = 'E'
                                 THEN
                                      (  cfd_sat.subtotal_xls
                                       - cfd_sat.descuento_xls)
                                    * (-1)
                                 WHEN cfd_sat.tipo_comprobante = 'I'
                                 THEN
                                      cfd_sat.subtotal_xls
                                    - cfd_sat.descuento_xls
                              END
                        END
                           AS neto_subtotal,
                        cfd_sat.tipo_comprobante,
                        CASE
                           WHEN     (cfd_sat.moneda_xls <> 'MXN')
                                AND (cfd_sat.tc_xls > 0)
                           THEN
                              CASE
                                 WHEN cfd_sat.tipo_comprobante = 'E'
                                 THEN
                                    (total_xls * (-1)) * cfd_sat.tc_xls
                                 WHEN cfd_sat.tipo_comprobante = 'I'
                                 THEN
                                    total_xls * cfd_sat.tc_xls
                              END
                           ELSE
                              CASE
                                 WHEN cfd_sat.tipo_comprobante = 'E'
                                 THEN
                                    cfd_sat.total_xls * (-1)
                                 WHEN cfd_sat.tipo_comprobante = 'I'
                                 THEN
                                    cfd_sat.total_xls
                              END
                        END
                           AS total,
                        cfd_sat.moneda_xls,
                        cfd_sat.tc_xls
                   FROM SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                  WHERE     (cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                               AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59')
                        AND (    (    (    cfd_sat.cancelado = 0
                                       AND cfd_sat.id_empresa_sat = 1)
                                  AND cfd_sat.tipo_comprobante IN ('E'))
                             AND year (cfd_sat.fecha) = 2020)) Subquery
                INNER JOIN
                SEGURIDAD_ERP.Contabilidad.proveedores_sat proveedores_sat
                   ON (Subquery.id_proveedor_sat = proveedores_sat.id)
         GROUP BY proveedores_sat.id,
                  proveedores_sat.rfc,
                  proveedores_sat.razon_social) Subquery_3
           ON (proveedores_sat.id = Subquery_3.id))
       LEFT OUTER JOIN
       (SELECT proveedores_sat.id,
               proveedores_sat.razon_social,
               proveedores_sat.rfc,
               SUM (Subquery.neto_subtotal) AS neto_subtotal_i,
               SUM (Subquery.total) AS neto_total_i
          FROM (SELECT cfd_sat.id_proveedor_sat,
                       cfd_sat.subtotal_xls AS subtotal,
                       cfd_sat.descuento_xls AS descuento,
                       CASE
                          WHEN     (cfd_sat.moneda_xls <> 'MXN')
                               AND (cfd_sat.tc_xls > 0)
                          THEN
                             CASE
                                WHEN cfd_sat.tipo_comprobante = 'E'
                                THEN
                                     (  (  cfd_sat.subtotal_xls
                                         - cfd_sat.descuento_xls)
                                      * (-1))
                                   * cfd_sat.tc_xls
                                WHEN cfd_sat.tipo_comprobante = 'I'
                                THEN
                                     cfd_sat.subtotal_xls
                                   - (cfd_sat.descuento_xls * cfd_sat.tc_xls)
                             END
                          ELSE
                             CASE
                                WHEN cfd_sat.tipo_comprobante = 'E'
                                THEN
                                     (  cfd_sat.subtotal_xls
                                      - cfd_sat.descuento_xls)
                                   * (-1)
                                WHEN cfd_sat.tipo_comprobante = 'I'
                                THEN
                                     cfd_sat.subtotal_xls
                                   - cfd_sat.descuento_xls
                             END
                       END
                          AS neto_subtotal,
                       cfd_sat.tipo_comprobante,
                       CASE
                          WHEN     (cfd_sat.moneda_xls <> 'MXN')
                               AND (cfd_sat.tc_xls > 0)
                          THEN
                             CASE
                                WHEN cfd_sat.tipo_comprobante = 'E'
                                THEN
                                   (total_xls * (-1)) * cfd_sat.tc_xls
                                WHEN cfd_sat.tipo_comprobante = 'I'
                                THEN
                                   total_xls * cfd_sat.tc_xls
                             END
                          ELSE
                             CASE
                                WHEN cfd_sat.tipo_comprobante = 'E'
                                THEN
                                   cfd_sat.total_xls * (-1)
                                WHEN cfd_sat.tipo_comprobante = 'I'
                                THEN
                                   cfd_sat.total_xls
                             END
                       END
                          AS total,
                       cfd_sat.moneda_xls,
                       cfd_sat.tc_xls
                  FROM SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                 WHERE     (cfd_sat.fecha BETWEEN '".$data["fecha_inicial"]->format("Y-m-d")." 00:00:00'
                                              AND '".$data["fecha_final"]->format("Y-m-d")." 23:59:59')
                       AND (    (    (    cfd_sat.cancelado = 0
                                      AND cfd_sat.id_empresa_sat = 1)
                                 AND cfd_sat.tipo_comprobante IN ('I'))
                            AND year (cfd_sat.fecha) = 2020)) Subquery
               INNER JOIN
               SEGURIDAD_ERP.Contabilidad.proveedores_sat proveedores_sat
                  ON (Subquery.id_proveedor_sat = proveedores_sat.id)
        GROUP BY proveedores_sat.id,
                 proveedores_sat.razon_social,
                 proveedores_sat.rfc) Subquery_2
          ON (proveedores_sat.id = Subquery_2.id)
GROUP BY proveedores_sat.id,
         proveedores_sat.rfc,
         proveedores_sat.razon_social,
         Subquery_1.neto_subtotal,
         Subquery_1.total,
         Subquery_2.neto_subtotal_i,
         Subquery_2.neto_total_i,
         Subquery_3.neto_total_e,
         Subquery_3.neto_subtotal_e) as reporte order by diferencia desc");
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function getListaCFDI($id_proveedor, $fecha_inicial, $fecha_final){

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
       configuracion_obra.nombre AS obra_sao
  FROM (((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
          LEFT OUTER JOIN
          SEGURIDAD_ERP.Finanzas.repositorio_facturas repositorio_facturas
             ON (cfd_sat.uuid = repositorio_facturas.uuid))
         LEFT OUTER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_1
            ON (cfd_sat.cfdi_relacionado = cfd_sat_1.uuid))
        LEFT OUTER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_2
           ON (cfd_sat.uuid = cfd_sat_2.cfdi_relacionado))
       LEFT OUTER JOIN
       SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
          ON     (repositorio_facturas.id_proyecto =
                     configuracion_obra.id_proyecto)
             AND (repositorio_facturas.id_obra = configuracion_obra.id_obra)
where cfd_sat.fecha BETWEEN '".$fecha_inicial->format("Y-m-d")." 00:00:00'
      AND '".$fecha_final->format("Y-m-d")." 23:59:59'
      AND cfd_sat.cancelado = 0
      AND cfd_sat.tipo_comprobante in('I','E')
      AND cfd_sat.id_proveedor_sat = ".$id_proveedor."
      AND cfd_sat.id_empresa_sat = 1
      order by cfd_sat.fecha
      "


        );

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

}
