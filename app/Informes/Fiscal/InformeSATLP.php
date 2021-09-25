<?php


namespace App\Informes\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\Reportes\CatalogoMeses;
use Illuminate\Support\Facades\DB;

class InformeSATLP
{
    public static function  get()
    {
        $informe = InformeSATLP::getInforme();
        return $informe;
    }

    public static function getCuentas($id_proveedor_sao)
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
 WHERE     (tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat = ".$id_proveedor_sao.")
       AND (cuentas_movimientos.tipo_poliza = 3)
       AND (cuentas_movimientos.tipo_movimiento = 'VERDADERO')
GROUP BY tmp_cuentas_contpaq_proveedores_sat.id_proveedor_sat,
         cuentas_movimientos.codigo_cuenta");

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    public static function  getInforme()
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
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

}
