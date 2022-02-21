<?php


namespace App\Informes;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfos;
use Illuminate\Support\Facades\DB;

class EFOSEmpresaInformeDesglosado
{
    public static function  getInforme()
    {
        $informe["informe"][] = EFOSEmpresaInformeDesglosado::getPartidasInformeDefinitivos();
        $informe["informe"][] = EFOSEmpresaInformeDesglosado::getPartidasInformePresuntos();
        $informe["informe"][] = EFOSEmpresaInformeDesglosado::getPartidasInformeEnAclaracion();
        $informe["informe"][] = EFOSEmpresaInformeDesglosado::getPartidasInformeAutocorregidos();
        $informe["informe"][] = EFOSEmpresaInformeDesglosado::getPartidasInformeNoDeducidos();
        $informe["informe"][] = EFOSEmpresaInformeDesglosado::getPartidasInformeDefinitivos2012();

        $informe["fechas"] = EFOSEmpresaInformeDesglosado::getFechasInforme();
        return $informe;
    }

    private static function getFechasInforme()
    {
        $fechas["lista_efos"]= ProcesamientoListaEfos::getFechaActualizacion();
        $fechas["cfd_recibidos"]= CFDSAT::getFechaUltimoCFDTxt();
        return $fechas;
    }

    private static function getPartidasInformePresuntos(){
        $informe = DB::select("SELECT ctg_estados_efos.descripcion AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_presunto_dof,103) as fecha_presunto_dof,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       ListaEmpresasSAT.nombre_corto AS empresa,
       year(cfd_sat.fecha) as anio,
       ListaEmpresasSAT.razon_social AS empresa_larga,
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
  FROM ((((SEGURIDAD_ERP.Fiscal.efos efos
           INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_estados_efos ctg_estados_efos
              ON (efos.estado = ctg_estados_efos.id))
          INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
             ON (efos.rfc = cfd_sat.rfc_emisor))
         INNER JOIN
         SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
            ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
        INNER JOIN
        (SELECT ListaEmpresasSAT.id,
                ListaEmpresasSAT.nombre_corto,
                MAX (ctg_efos.fecha_presunto) AS fecha_presunto_maxima
           FROM ((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                  INNER JOIN
                  SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
                     ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
                 INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
                    ON (efos.rfc = cfd_sat.rfc_emisor))
                INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
                   ON (ctg_efos.rfc = efos.rfc)
          WHERE (ctg_efos.estado = 2)
         GROUP BY ListaEmpresasSAT.id, ListaEmpresasSAT.nombre_corto)
        Subquery
           ON (Subquery.id = ListaEmpresasSAT.id))
       INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
          ON (ctg_efos.rfc = efos.rfc)
       INNER JOIN (
          select max(id) as id from SEGURIDAD_ERP.Fiscal.ctg_efos
              group by rfc
              ) as ctg_efos_maximo
          ON (ctg_efos.id = ctg_efos_maximo.id)
 WHERE (efos.estado = 2) and cfd_sat.tipo_comprobante != 'P' and ctg_efos.estado_registro = 1
 AND cfd_sat.cancelado != 1
       AND cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ListaEmpresasSAT.razon_social,
         year(cfd_sat.fecha),
         ctg_efos.fecha_presunto,
         ctg_efos.fecha_presunto_dof,
         Subquery.fecha_presunto_maxima
ORDER BY Subquery.fecha_presunto_maxima DESC,
         empresa ASC,
         year(cfd_sat.fecha) DESC,
         ctg_efos.fecha_presunto DESC")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOSEmpresaInformeDesglosado::setSubtotalesPartidas($informe, "PRESUNTOS");
        return $informe;
    }

    private static function getPartidasInformeAutocorregidos(){
        $informe = DB::select("
        SELECT 'Corregido' AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       CONVERT(varchar,Subquery.fecha_autocorreccion,103)  as fecha_autocorreccion,
       ListaEmpresasSAT.nombre_corto AS empresa,
       year(cfd_sat.fecha) as anio,
       ListaEmpresasSAT.razon_social AS empresa_larga,
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
  FROM ((((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
           INNER JOIN
           (SELECT cfd_sat.id, cfd_sat.estado, cfd_sat_autocorrecciones.fecha_autocorreccion
              FROM SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones cfd_sat_autocorrecciones
                   INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                      ON (cfd_sat_autocorrecciones.id_cfd_sat = cfd_sat.id)
             WHERE (cfd_sat.estado = 6)) Subquery
              ON (cfd_sat.id = Subquery.id))
          INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
             ON (efos.rfc = cfd_sat.rfc_emisor))
         INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_estados_efos ctg_estados_efos
            ON (efos.estado = ctg_estados_efos.id))
        INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
           ON (ctg_efos.rfc = efos.rfc)
        INNER JOIN (
          select max(id) as id from SEGURIDAD_ERP.Fiscal.ctg_efos
              group by rfc
              ) as ctg_efos_maximo
          ON (ctg_efos.id = ctg_efos_maximo.id)
        )
       INNER JOIN
       SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
          ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id)
 WHERE (ctg_efos.estado_registro = 1) AND efos.estado = 0
 AND cfd_sat.cancelado != 1
       AND cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         year(cfd_sat.fecha),
         ListaEmpresasSAT.razon_social,
         Subquery.fecha_autocorreccion,
         ctg_efos.fecha_presunto
ORDER BY 7 DESC, 8 DESC
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOSEmpresaInformeDesglosado::setSubtotalesPartidas($informe, "CORREGIDOS");
        return $informe;
    }

    private static function getPartidasInformeEnAclaracion(){
        $informe = DB::select("
        SELECT 'En Aclaración SAT' AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       CONVERT(varchar,ctg_efos.fecha_definitivo_dof,103)  as fecha_definitivo_dof,
       ListaEmpresasSAT.nombre_corto AS empresa,
       year(cfd_sat.fecha) as anio,
       ListaEmpresasSAT.razon_social AS empresa_larga,
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
  FROM ((((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
           INNER JOIN
           (SELECT cfd_sat.id, cfd_sat.estado
              FROM SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones cfd_sat_autocorrecciones
                   INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                      ON (cfd_sat_autocorrecciones.id_cfd_sat = cfd_sat.id)
             WHERE (cfd_sat.estado = 5)) Subquery
              ON (cfd_sat.id = Subquery.id))
          INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
             ON (efos.rfc = cfd_sat.rfc_emisor))
         INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_estados_efos ctg_estados_efos
            ON (efos.estado = ctg_estados_efos.id))
        INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
           ON (ctg_efos.rfc = efos.rfc)
        INNER JOIN (
          select max(id) as id from SEGURIDAD_ERP.Fiscal.ctg_efos
              group by rfc
              ) as ctg_efos_maximo
          ON (ctg_efos.id = ctg_efos_maximo.id)
           )
       INNER JOIN
       SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
          ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id)
 WHERE (ctg_efos.estado_registro = 1) AND efos.estado = 0
 AND cfd_sat.cancelado != 1
       AND cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         year(cfd_sat.fecha),
         ListaEmpresasSAT.razon_social,
         ctg_efos.fecha_definitivo,
         ctg_efos.fecha_definitivo_dof,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto
ORDER BY 7 DESC,8 DESC
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOSEmpresaInformeDesglosado::setSubtotalesPartidas($informe, "EN ACLARACIÓN");
        return $informe;
    }

    private static function getPartidasInformeNoDeducidos(){
        $informe = DB::select("
        SELECT 'No Deducidos' AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       CONVERT(varchar,ctg_efos.fecha_definitivo_dof,103)  as fecha_definitivo_dof,
       ListaEmpresasSAT.nombre_corto AS empresa,
       year(cfd_sat.fecha) as anio,
       ListaEmpresasSAT.razon_social AS empresa_larga,
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
  FROM ((((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
           INNER JOIN
           (SELECT cfd_sat.id, cfd_sat.estado
              FROM SEGURIDAD_ERP.Fiscal.cfd_no_deducidos cfd_no_deducidos
                   INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                      ON (cfd_no_deducidos.id_cfd_sat = cfd_sat.id)
             WHERE (cfd_sat.estado = 7)) Subquery
              ON (cfd_sat.id = Subquery.id))
          INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
             ON (efos.rfc = cfd_sat.rfc_emisor))
         INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_estados_efos ctg_estados_efos
            ON (efos.estado = ctg_estados_efos.id))
        INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
           ON (ctg_efos.rfc = efos.rfc)
        INNER JOIN (
          select max(id) as id from SEGURIDAD_ERP.Fiscal.ctg_efos
              group by rfc
              ) as ctg_efos_maximo
          ON (ctg_efos.id = ctg_efos_maximo.id)
           )
       INNER JOIN
       SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
          ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id)
 WHERE (ctg_efos.estado_registro = 1) AND efos.estado = 0
 AND cfd_sat.cancelado != 1
       AND cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ctg_efos.fecha_definitivo_dof,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto,
         year(cfd_sat.fecha),
         ListaEmpresasSAT.razon_social
ORDER BY 7 DESC, 8 DESC
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOSEmpresaInformeDesglosado::setSubtotalesPartidas($informe, "NO DEDUCIDOS");
        return $informe;
    }

    private static function getPartidasInformeDefinitivos(){
        $informe = DB::select("
        SELECT ctg_estados_efos.descripcion AS estatus,
       efos.rfc,
       efos.razon_social,
       year(cfd_sat.fecha) as anio,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       CONVERT(varchar,ctg_efos.fecha_definitivo_dof,103)  as fecha_definitivo_dof,
       ListaEmpresasSAT.nombre_corto AS empresa,
       ListaEmpresasSAT.razon_social AS empresa_larga,

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
          AS importe,
       Subquery.fecha_devinitivo_maxima
  FROM ((((((SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
             INNER JOIN
             (SELECT ListaEmpresasSAT.id,
                     ListaEmpresasSAT.nombre_corto,
                     MAX (ctg_efos.fecha_definitivo)
                        AS fecha_devinitivo_maxima
                FROM ((SEGURIDAD_ERP.Fiscal.efos efos
                       INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
                          ON (efos.rfc = ctg_efos.rfc))
                      INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                         ON (cfd_sat.rfc_emisor = efos.rfc))
                     INNER JOIN
                     SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
                        ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id)
               WHERE ctg_efos.estado = 0
              GROUP BY ListaEmpresasSAT.id, ListaEmpresasSAT.nombre_corto)
             Subquery
                ON (ListaEmpresasSAT.id = Subquery.id))
            INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
               ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
           INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
              ON (efos.rfc = cfd_sat.rfc_emisor))
          INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
             ON (ctg_efos.rfc = efos.rfc)
          INNER JOIN (
          select max(id) as id from SEGURIDAD_ERP.Fiscal.ctg_efos
              group by rfc
              ) as ctg_efos_maximo
          ON (ctg_efos.id = ctg_efos_maximo.id)
             )
         INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_estados_efos ctg_estados_efos
            ON     (efos.estado = ctg_estados_efos.id)
               AND (ctg_efos.estado = ctg_estados_efos.id))
        LEFT OUTER JOIN
        SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones cfd_sat_autocorrecciones
           ON (cfd_sat_autocorrecciones.id_cfd_sat = cfd_sat.id))
       LEFT OUTER JOIN SEGURIDAD_ERP.Fiscal.cfd_no_deducidos cfd_no_deducidos
          ON (cfd_no_deducidos.id_cfd_sat = cfd_sat.id)
 WHERE     (ctg_efos.estado_registro = 1)
       AND (cfd_sat_autocorrecciones.id IS NULL)
       AND (cfd_no_deducidos.id IS NULL)
       AND (efos.estado = 0)
       AND (cfd_sat.estado !=8)
       AND cfd_sat.cancelado != 1
       AND cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ListaEmpresasSAT.razon_social,
         ctg_efos.fecha_presunto,
         ctg_efos.fecha_definitivo_dof,
         Subquery.fecha_devinitivo_maxima,
         year(cfd_sat.fecha)
ORDER BY Subquery.fecha_devinitivo_maxima DESC,
         empresa ASC,
         year(cfd_sat.fecha) DESC,
         ctg_efos.fecha_definitivo DESC
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOSEmpresaInformeDesglosado::setSubtotalesPartidas($informe, "DEFINITIVOS");
        return $informe;
    }

    private static function getPartidasInformeDefinitivos2012(){
        $informe = DB::select("
        SELECT 'Definitivos Antes 2012' AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       CONVERT(varchar,ctg_efos.fecha_definitivo_dof,103)  as fecha_definitivo_dof,
       ListaEmpresasSAT.nombre_corto AS empresa,
       year(cfd_sat.fecha) as anio,
       ListaEmpresasSAT.razon_social AS empresa_larga,
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
          AS importe,
       Subquery.fecha_devinitivo_maxima
  FROM ((((((SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
             INNER JOIN
             (SELECT ListaEmpresasSAT.id,
                     ListaEmpresasSAT.nombre_corto,
                     MAX (ctg_efos.fecha_definitivo)
                        AS fecha_devinitivo_maxima
                FROM ((SEGURIDAD_ERP.Fiscal.efos efos
                       INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
                          ON (efos.rfc = ctg_efos.rfc))
                      INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                         ON (cfd_sat.rfc_emisor = efos.rfc))
                     INNER JOIN
                     SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
                        ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id)
               WHERE ctg_efos.estado = 0
              GROUP BY ListaEmpresasSAT.id, ListaEmpresasSAT.nombre_corto)
             Subquery
                ON (ListaEmpresasSAT.id = Subquery.id))
            INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
               ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
           INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
              ON (efos.rfc = cfd_sat.rfc_emisor))
          INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
             ON (ctg_efos.rfc = efos.rfc)
          INNER JOIN (
          select max(id) as id from SEGURIDAD_ERP.Fiscal.ctg_efos
              group by rfc
              ) as ctg_efos_maximo
          ON (ctg_efos.id = ctg_efos_maximo.id)
             )
         INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_estados_efos ctg_estados_efos
            ON     (efos.estado = ctg_estados_efos.id)
               AND (ctg_efos.estado = ctg_estados_efos.id))
        LEFT OUTER JOIN
        SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones cfd_sat_autocorrecciones
           ON (cfd_sat_autocorrecciones.id_cfd_sat = cfd_sat.id))
       LEFT OUTER JOIN SEGURIDAD_ERP.Fiscal.cfd_no_deducidos cfd_no_deducidos
          ON (cfd_no_deducidos.id_cfd_sat = cfd_sat.id)
 WHERE     (ctg_efos.estado_registro = 1)
       AND (cfd_sat_autocorrecciones.id IS NULL)
       AND (cfd_no_deducidos.id IS NULL)
       AND (efos.estado = 0)
       AND (cfd_sat.estado =8)
       AND cfd_sat.cancelado != 1
       AND cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto,
         ctg_efos.fecha_definitivo_dof,
         Subquery.fecha_devinitivo_maxima,
         year(cfd_sat.fecha),
       ListaEmpresasSAT.razon_social
ORDER BY Subquery.fecha_devinitivo_maxima DESC,
         empresa ASC,
         ctg_efos.fecha_definitivo DESC,
         year(cfd_sat.fecha) DESC
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOSEmpresaInformeDesglosado::setSubtotalesPartidas($informe, "DEFINITIVOS ANTES 2012");
        return $informe;
    }

    private static function setSubtotalesPartidas($partidas, $tipo){
        $partidas_completas = [];
        if(count($partidas)>0){
            $i = 0;
            $contador_partidas_empresa = 1;
            $contador_partidas_anio = 1;
            $contador_cfdi = 0;
            $importe_cfdi=0;
            $contador_cfdi_anio = 0;
            $importe_cfdi_anio =0;
            $contador_cfdi_global = 0;
            $importe_cfdi_global=0;
            $i_bis = 1;
            $i_p =0;
            $acumulador_partidas_empresa = 0;

            $partidas_completas[$i]["etiqueta"] = $tipo;
            $partidas_completas[$i]["tipo"] = "titulo";
            $partidas_completas[$i]["bg_color_hex"] = "#FFF";
            $partidas_completas[$i]["bg_color_rgb"] = [255,255,255];
            $partidas_completas[$i]["color_hex"] = "#000";
            $partidas_completas[$i]["color_rgb"] = [0,0,0];
            $i++;

            $partidas_completas[$i]["etiqueta"] = $partidas[0]["empresa_larga"];
            $partidas_completas[$i]["tipo"] = "subtitulo";
            $partidas_completas[$i]["bg_color_hex"] = "#757575";
            $partidas_completas[$i]["bg_color_rgb"] = [213,213,213];
            $partidas_completas[$i]["color_hex"] = "#FFF";
            $partidas_completas[$i]["color_rgb"] = [255,255,255];
            $i++;

            $partidas_completas[$i]["etiqueta"] = $partidas[0]["anio"];
            $partidas_completas[$i]["tipo"] = "subtitulo_final";
            $partidas_completas[$i]["bg_color_hex"] = "#757575";
            $partidas_completas[$i]["bg_color_rgb"] = [213,213,213];
            $partidas_completas[$i]["color_hex"] = "#FFF";
            $partidas_completas[$i]["color_rgb"] = [255,255,255];
            $i++;

            foreach($partidas as $partida)
            {
                if($i_p>0){

                    if($partida["anio"]!=$partidas[$i_p-1]["anio"] || $partida["empresa"]!=$partidas[$i_p-1]["empresa"]) {

                        $partidas_completas[$i]["contador"] = $contador_partidas_anio - 1;
                        //$partidas_completas[$i]["acumulador"] = $acumulador_partidas_empresa;
                        $partidas_completas[$i]["etiqueta"] = "SUBTOTAL " . $tipo . " " . $partidas[$i_p - 1]["empresa"] . " " . $partidas[$i_p - 1]["anio"];
                        $partidas_completas[$i]["contador_cfdi"] = $contador_cfdi_anio;
                        $partidas_completas[$i]["importe"] = $importe_cfdi_anio;
                        $partidas_completas[$i]["importe_format"] = '$ ' . number_format($importe_cfdi_anio, 2, ".", ",");
                        $partidas_completas[$i]["tipo"] = "subtotal";
                        /*$partidas_completas[$i]["bg_color_hex"] = "#d5d5d5";
                        $partidas_completas[$i]["bg_color_rgb"] = [213, 213, 213];
                        $partidas_completas[$i]["color_hex"] = "#000";
                        $partidas_completas[$i]["color_rgb"] = [0, 0, 0];*/

                        $partidas_completas[$i]["bg_color_hex"] = "#757575";
                        $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
                        $partidas_completas[$i]["color_hex"] = "#FFF";
                        $partidas_completas[$i]["color_rgb"] = [255,255,255];

                        $i++;

                        if ($partida["empresa"] == $partidas[$i_p - 1]["empresa"]){
                            $partidas_completas[$i]["etiqueta"] = $partidas[$i_p]["anio"];
                            $partidas_completas[$i]["tipo"] = "subtitulo_final";
                            $partidas_completas[$i]["bg_color_hex"] = "#757575";
                            $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
                            $partidas_completas[$i]["color_hex"] = "#FFF";
                            $partidas_completas[$i]["color_rgb"] = [255,255,255];
                            $i++;
                            $contador_partidas_anio = 1;
                            $contador_cfdi_anio=0;
                            $importe_cfdi_anio=0;
                        }
                    }


                    if($partida["empresa"]!=$partidas[$i_p-1]["empresa"] ){

                        $partidas_completas[$i]["contador"] = $contador_partidas_empresa-1;
                        $partidas_completas[$i]["acumulador"] = $acumulador_partidas_empresa;
                        $partidas_completas[$i]["etiqueta"] = "SUBTOTAL ".$tipo." ".$partidas[$i_p-1]["empresa"];
                        $partidas_completas[$i]["contador_cfdi"] = $contador_cfdi;
                        $partidas_completas[$i]["importe"] = $importe_cfdi;
                        $partidas_completas[$i]["importe_format"] = '$ '.number_format($importe_cfdi,2,".",",");
                        $partidas_completas[$i]["tipo"] = "subtotal";
                        $partidas_completas[$i]["bg_color_hex"] = "#757575";
                        $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
                        $partidas_completas[$i]["color_hex"] = "#FFF";
                        $partidas_completas[$i]["color_rgb"] = [255,255,255];
                        $i++;



                        $partidas_completas[$i]["etiqueta"] = $partidas[$i_p]["empresa_larga"];
                        $partidas_completas[$i]["tipo"] = "subtitulo";
                        $partidas_completas[$i]["bg_color_hex"] = "#757575";
                        $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
                        $partidas_completas[$i]["color_hex"] = "#FFF";
                        $partidas_completas[$i]["color_rgb"] = [255,255,255];
                        $i++;

                        $partidas_completas[$i]["etiqueta"] = $partidas[$i_p]["anio"];
                        $partidas_completas[$i]["tipo"] = "subtitulo_final";
                        $partidas_completas[$i]["bg_color_hex"] = "#757575";
                        $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
                        $partidas_completas[$i]["color_hex"] = "#FFF";
                        $partidas_completas[$i]["color_rgb"] = [255,255,255];
                        $i++;

                        $contador_partidas_empresa = 1;
                        $contador_partidas_anio = 1;
                        $contador_cfdi=0;
                        $importe_cfdi=0;
                        $contador_cfdi_anio=0;
                        $importe_cfdi_anio=0;
                        $acumulador_partidas_empresa=0;
                    }
                }

                $partidas_completas[$i] = $partida;
                $partidas_completas[$i]["indice"] = $i_bis;
                $partidas_completas[$i]["tipo"] = "partida";
                $contador_cfdi+=$partidas_completas[$i]["no_CFDI"];
                $importe_cfdi+=$partidas_completas[$i]["importe"];
                $contador_cfdi_anio+=$partidas_completas[$i]["no_CFDI"];
                $importe_cfdi_anio+=$partidas_completas[$i]["importe"];
                $contador_cfdi_global+=$partidas_completas[$i]["no_CFDI"];;
                $importe_cfdi_global+=$partidas_completas[$i]["importe"];
                $contador_partidas_empresa++;
                $contador_partidas_anio++;
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
            $partidas_completas[$i]["bg_color_hex"] = "#757575";
            $partidas_completas[$i]["bg_color_rgb"] = [117,117,117];
            $partidas_completas[$i]["color_hex"] = "#FFF";
            $partidas_completas[$i]["color_rgb"] = [255,255,255];
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
