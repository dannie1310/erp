<?php


namespace App\Informes\Fiscal\Chatbot;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\Reportes\CatalogoMeses;
use Illuminate\Support\Facades\DB;

class InformeDetalleUltimosCambiosEFOS
{
    public static function  get($rfc_efos)
    {
        $partidas = InformeDetalleUltimosCambiosEFOS::getPartidas($rfc_efos);

        return $partidas;
    }

    public static function getPartidas($rfc_efos){
        $pendientes_correccion = InformeDetalleUltimosCambiosEFOS::getPendientesCorreccion($rfc_efos);
        $en_aclaracion = InformeDetalleUltimosCambiosEFOS::getEnAclaracion($rfc_efos);
        $corregidos = InformeDetalleUltimosCambiosEFOS::getCorregidos($rfc_efos);
        $no_deducidos = InformeDetalleUltimosCambiosEFOS::getNoDeducidos($rfc_efos);
        $presuntos = InformeDetalleUltimosCambiosEFOS::getPresuntos($rfc_efos);

        return [
            "pendientes"=>$pendientes_correccion,
            "en_aclaracion"=>$en_aclaracion,
            "corregidos"=>$corregidos,
            "no_deducidos"=>$no_deducidos,
            "presuntos"=>$presuntos,
        ];

    }

    public static function getTotal($rfc_efos)
    {
        $total = DB::select("SELECT
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,
       sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END)
        AS importe,

    format (
    sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END),
          'C') AS importe_format
  FROM SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat

 WHERE cfd_sat.cancelado != 1 and cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
AND cfd_sat.rfc_emisor ='".$rfc_efos."'

ORDER BY 2 DESC")
        ;
        $total = array_map(function ($value) {
            return (array)$value;
        }, $total);

        return $total[0];
    }

    public static function getPendientesCorreccion($rfc_efos){

        $partidas = DB::select("SELECT ctg_estados_efos.descripcion AS estatus,
       ListaEmpresasSAT.nombre_corto AS empresa,
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,

        sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END)
        AS importe,

    format (
    sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END),
          'C') AS importe_format

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
       AND cfd_sat.rfc_emisor ='".$rfc_efos."'
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto,
         ctg_efos.fecha_definitivo_dof,
         Subquery.fecha_devinitivo_maxima,
         efos.fecha_limite_dof,
         efos.fecha_limite_sat
ORDER BY Subquery.fecha_devinitivo_maxima DESC,
         empresa ASC,
         ctg_efos.fecha_definitivo DESC")
        ;
        $partidas = array_map(function ($value) {
            return (array)$value;
        }, $partidas);

        return $partidas;

    }
    public static function getEnAclaracion($rfc_efos){

        $partidas = DB::select("SELECT 'En Aclaración SAT' AS estatus,
       ListaEmpresasSAT.nombre_corto AS empresa,
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,
       sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END)
        AS importe,

    format (
    sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END),
          'C') AS importe_format
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
 WHERE (ctg_efos.estado_registro = 1) AND efos.estado = 0 AND cfd_sat.cancelado != 1 and cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
AND cfd_sat.rfc_emisor ='".$rfc_efos."'
 GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ctg_efos.fecha_definitivo_dof,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto
ORDER BY 2 DESC")
        ;
        $partidas = array_map(function ($value) {
            return (array)$value;
        }, $partidas);

        return $partidas;

    }
    public static function getCorregidos($rfc_efos){

        $partidas = DB::select("
        SELECT 'Corregido' AS estatus,
       ListaEmpresasSAT.nombre_corto AS empresa,
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,
       sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END)
        AS importe,

    format (
    sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END),
          'C') AS importe_format
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
 WHERE (ctg_efos.estado_registro = 1) AND efos.estado = 0 AND cfd_sat.cancelado != 1 and cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
AND cfd_sat.rfc_emisor ='".$rfc_efos."'
 GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         Subquery.fecha_autocorreccion,
         ctg_efos.fecha_presunto
ORDER BY 2 DESC
        ")
        ;
        $partidas = array_map(function ($value) {
            return (array)$value;
        }, $partidas);

        return $partidas;

    }
    public static function getNoDeducidos($rfc_efos){

        $partidas = DB::select("
        SELECT 'No Deducidos' AS estatus,
       ListaEmpresasSAT.nombre_corto AS empresa,
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,
       sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END)
        AS importe,

    format (
    sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END),
          'C') AS importe_format
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
 WHERE (ctg_efos.estado_registro = 1) AND efos.estado = 0 AND cfd_sat.cancelado != 1 and cfd_sat.tipo_comprobante != 'P' and cfd_sat.tipo_comprobante != 'T'
AND cfd_sat.rfc_emisor ='".$rfc_efos."'
 GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ctg_efos.fecha_definitivo_dof,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto
ORDER BY 2 DESC
        ")
        ;
        $partidas = array_map(function ($value) {
            return (array)$value;
        }, $partidas);

        return $partidas;

    }
    public static function getPresuntos($rfc_efos){

        $partidas = DB::select("
        SELECT ctg_estados_efos.descripcion AS estatus,
ListaEmpresasSAT.nombre_corto AS empresa,
       COUNT (DISTINCT cfd_sat.id) AS no_CFDI,
       sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END)
        AS importe,

    format (
    sum(CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN cfd_sat.total * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN cfd_sat.total
            END
        END),
          'C') AS importe_format
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
 WHERE (efos.estado = 2) and cfd_sat.tipo_comprobante != 'P' and cfd_sat.cancelado != 1 and ctg_efos.estado_registro = 1 and cfd_sat.tipo_comprobante != 'T'
AND cfd_sat.rfc_emisor ='".$rfc_efos."'
 GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto,
         ctg_efos.fecha_presunto_dof,
         Subquery.fecha_presunto_maxima
ORDER BY Subquery.fecha_presunto_maxima DESC,
         empresa ASC,
         ctg_efos.fecha_presunto DESC
        ")
        ;
        $partidas = array_map(function ($value) {
            return (array)$value;
        }, $partidas);

        return $partidas;

    }
}
