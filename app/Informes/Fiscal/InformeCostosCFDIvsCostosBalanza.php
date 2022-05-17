<?php


namespace App\Informes\Fiscal;


use App\Informes\CFDICompleto;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCosto;
use App\Models\SEGURIDAD_ERP\Reportes\CatalogoMeses;
use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InformeCostosCFDIvsCostosBalanza
{
    public static function  get($data)
    {
        $informe["ultima_verificacion"] = InformeCostosCFDIvsCostosBalanza::getUltimaVerificacionCFDI();
        $informe["anio"] = $data["anio"];
        $informe["partidas"] = InformeCostosCFDIvsCostosBalanza::getInforme($data);
        $informe["empresas_sat"] = InformeCostosCFDIvsCostosBalanza::getEmpresasSAT();
        $informe["empresa"] = InformeCostosCFDIvsCostosBalanza::getEmpresa($data);
        $informe["sumatorias"] = InformeCostosCFDIvsCostosBalanza::getSumatoria($informe["partidas"]);
        return $informe;
    }

    public static function getUltimaVerificacionCFDI()
    {

        $ultima_fecha = DB::connection("seguridad")->select("
            select max(ultima_verificacion) as ultima_verificacion
            from Contabilidad.cfd_sat
        ");


        $ultima_verificacion_dt = new DateTime($ultima_fecha[0]->ultima_verificacion);



        $fechas_cfdi = DB::connection("seguridad")->select("
            select min(fecha) as fecha_minima,  max(fecha) as fecha_maxima
            from Contabilidad.cfd_sat
where ultima_verificacion between '".$ultima_verificacion_dt->format("Y-m-d")." 00:00:00'
and '".$ultima_verificacion_dt->format("Y-m-d")." 23:59:59'
        ");

        $fecha_inicial_cfdi_dt = new DateTime($fechas_cfdi[0]->fecha_minima);
        $fecha_final_cfdi_dt = new DateTime($fechas_cfdi[0]->fecha_maxima);

        $ultima_fecha_verificacion = $ultima_verificacion_dt->format("d/m/Y");
        $fecha_inicial_cfdi = $fecha_inicial_cfdi_dt->format("d/m/Y");
        $fecha_final_cfdi = $fecha_final_cfdi_dt->format("d/m/Y");

        return [
            "ultima_fecha_verificacion" => $ultima_fecha_verificacion,
            "fecha_inicial_cfdi" =>$fecha_inicial_cfdi,
            "fecha_final_cfdi" =>$fecha_final_cfdi
        ];

    }

    public static function getEmpresa($data)
    {
        $alias_bdd = InformeCostosCFDIvsCostosBalanza::getAliasBDD($data["empresa_sat"]);
        $empresaSAT = EmpresaSAT::find($data["empresa_sat"])->razon_social;
        return  ($alias_bdd) ? "Año: ".$data["anio"]." Empresa: ".$empresaSAT ." (".$alias_bdd.")":"Año: ".$data["anio"]." Empresa: ".$empresaSAT ;
    }

    public static function getEmpresasSAT()
    {
        $informe = DB::connection("seguridad")->select("
    SELECT
        les.id as id,
        les.razon_social +' ('+ le.AliasBDD+')' as label,
        les.razon_social as customLabel
    FROM
        SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT as les join
        SEGURIDAD_ERP.Contabilidad.ListaEmpresas as le on(les.id = le.IdEmpresaSAT)
    WHERE
        le.Consolidadora = 1 and le.Historica = 0 and le.Desarrollo = 0
    group by les.id, les.razon_social, le.AliasBDD
    ORDER BY les.razon_social;
");
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return collect($informe);
    }

    public static function getSumatoria($partidas)
    {
        $suma_costos_balanza = 0;
        $suma_costos_cfdi = 0;
        $suma_costos_cfdi_i = 0;
        $suma_sustitucion_ejercicios_anteriores = 0;
        $suma_compraventa_divisas = 0;
        $suma_dispersion_vales = 0;
        $suma_neto_tipo_i = 0;
        $suma_costos_cfdi_e = 0;
        $suma_relacion_ejercicios_anteriores = 0;
        $suma_relacion_ejercicios_posteriores = 0;
        $suma_neto_tipo_e = 0;
        $suma_diferencia = 0;

        foreach($partidas as $partida)
        {
            $suma_costos_balanza += $partida["costo_balanza_sf"];
            $suma_costos_cfdi += $partida["costo_cfdi_sf"];
            $suma_costos_cfdi_i += $partida["costo_cfdi_i_sf"];
            $suma_sustitucion_ejercicios_anteriores += $partida["sustitucion_ejercicios_anteriores_sf"];
            $suma_compraventa_divisas += $partida["compraventa_divisas_sf"];
            $suma_dispersion_vales+=  $partida["dispersion_vales_sf"]  ;
            $suma_neto_tipo_i += $partida["neto_tipo_i_sf"];
            $suma_costos_cfdi_e += $partida["costo_cfdi_e_sf"];
            $suma_relacion_ejercicios_anteriores += $partida["relacion_ejercicios_anteriores_sf"];
            $suma_relacion_ejercicios_posteriores += $partida["relacion_ejercicios_posteriores_sf"];
            $suma_neto_tipo_e += $partida["neto_tipo_e_sf"];
            $suma_diferencia += $partida["diferencia_sf"];
        }

        return array(
            "suma_costos_balanza"=>number_format($suma_costos_balanza,2),
            "suma_costos_cfdi"=>number_format($suma_costos_cfdi,2),
            "suma_costos_cfdi_i"=>number_format($suma_costos_cfdi_i,2),
            "suma_sustitucion_ejercicios_anteriores"=>number_format($suma_sustitucion_ejercicios_anteriores,2),
            "suma_compraventa_divisas"=>number_format($suma_compraventa_divisas,2),
            "suma_dispersion_vales"=>number_format($suma_dispersion_vales,2),
            "suma_neto_tipo_i"=>number_format($suma_neto_tipo_i,2),
            "suma_costos_cfdi_e"=>number_format($suma_costos_cfdi_e,2),
            "suma_relacion_ejercicios_anteriores"=>number_format($suma_relacion_ejercicios_anteriores,2),
            "suma_relacion_ejercicios_posteriores"=>number_format($suma_relacion_ejercicios_posteriores,2),
            "suma_neto_tipo_e"=>number_format($suma_neto_tipo_e,2),
            "suma_diferencia"=>number_format($suma_diferencia,2),

            "suma_costos_balanza_sf"=>$suma_costos_balanza,
            "suma_costos_cfdi_sf"=>$suma_costos_cfdi,

            "suma_costos_cfdi_i_sf"=>$suma_costos_cfdi_i,
            "suma_costos_cfdi_e_sf"=>$suma_costos_cfdi_e,


            "suma_sustitucion_ejercicios_anteriores_sf"=>$suma_sustitucion_ejercicios_anteriores,
            "suma_compraventa_divisas_sf"=>$suma_compraventa_divisas,
            "suma_dispersion_vales_sf"=>$suma_dispersion_vales,
            "suma_relacion_ejercicios_anteriores_sf"=>$suma_relacion_ejercicios_anteriores,
            "suma_relacion_ejercicios_posteriores_sf"=>$suma_relacion_ejercicios_posteriores,
            "suma_neto_tipo_i_sf"=>$suma_neto_tipo_i,
            "suma_neto_tipo_e_sf"=>$suma_neto_tipo_e,
            "suma_diferencia_sf"=>$suma_diferencia,
        );

    }

    public static function getInforme($data)
    {
        $informe = [];
        //$costos_cfdi_ini = InformeCostosCFDIvsCostosBalanza::getCostoCFDI($data);
        $costos_cfdi_i_ini = InformeCostosCFDIvsCostosBalanza::getCostoCFDII($data);
        $costos_cfdi_e_ini = InformeCostosCFDIvsCostosBalanza::getCostoCFDIE($data);
        $costos_balanza_ini = InformeCostosCFDIvsCostosBalanza::getCostoBalanza($data);
        $sustituciones_ejercicios_anteriores_ini = InformeCostosCFDIvsCostosBalanza::getSustitucionEjerciciosAnteriores($data);
        $compraventas_divisas_ini = InformeCostosCFDIvsCostosBalanza::getCompraVentaDivisas($data);
        $dispersiones_vales_ini = InformeCostosCFDIvsCostosBalanza::getDispersionVales($data);
        $relaciones_ejercicios_anteriores_ini = InformeCostosCFDIvsCostosBalanza::getRelacionEjerciciosAnteriores($data);
        $relaciones_ejercicios_posteriores_ini = InformeCostosCFDIvsCostosBalanza::getRelacionEjerciciosPosteriores($data);

        $costo_cfdi = [];
        $costo_cfdi_i = [];
        $costo_cfdi_e = [];
        $costo_balanza = [];
        $sustitucion_ejercicios_anteriores = [];
        $compraventa_divisas = [];
        $dispersion_vales = [];
        $relacion_ejercicios_anteriores = [];
        $relacion_ejercicios_posteriores = [];
        $neto_tipo_i = [];
        $neto_tipo_e = [];
        $diferencia = [];

        foreach($costos_cfdi_i_ini as $costo_cfdi_i_ini)
        {
            $costo_cfdi_i[$costo_cfdi_i_ini["mes"]] = $costo_cfdi_i_ini["cfdi_i_recibidos"];
            $neto_tipo_i[$costo_cfdi_i_ini["mes"]] = $costo_cfdi_i_ini["cfdi_i_recibidos"];
            $costo_cfdi[$costo_cfdi_i_ini["mes"]] = $costo_cfdi_i_ini["cfdi_i_recibidos"];
            $diferencia[$costo_cfdi_i_ini["mes"]] = $costo_cfdi_i_ini["cfdi_i_recibidos"];
        }

        foreach($sustituciones_ejercicios_anteriores_ini as $sustitucion_ejercicios_anteriores_ini)
        {
            $sustitucion_ejercicios_anteriores[$sustitucion_ejercicios_anteriores_ini["mes"]] = $sustitucion_ejercicios_anteriores_ini["neto_subtotal"];
            $neto_tipo_i[$sustitucion_ejercicios_anteriores_ini["mes"]] -= $sustitucion_ejercicios_anteriores_ini["neto_subtotal"];
            $costo_cfdi[$sustitucion_ejercicios_anteriores_ini["mes"]] -= $sustitucion_ejercicios_anteriores_ini["neto_subtotal"];
            $diferencia[$sustitucion_ejercicios_anteriores_ini["mes"]] -= $sustitucion_ejercicios_anteriores_ini["neto_subtotal"];
        }

        foreach($compraventas_divisas_ini as $compraventa_divisas_ini)
        {
            $compraventa_divisas[$compraventa_divisas_ini["mes"]] = $compraventa_divisas_ini["neto_subtotal"];
            $neto_tipo_i[$compraventa_divisas_ini["mes"]] -= $compraventa_divisas_ini["neto_subtotal"];
            $costo_cfdi[$compraventa_divisas_ini["mes"]] -= $compraventa_divisas_ini["neto_subtotal"];
            $diferencia[$compraventa_divisas_ini["mes"]] -= $compraventa_divisas_ini["neto_subtotal"];
        }

        foreach($dispersiones_vales_ini as $dispersion_vales_ini)
        {
            $dispersion_vales[$dispersion_vales_ini["mes"]] = $dispersion_vales_ini["neto_subtotal"];
            $neto_tipo_i[$dispersion_vales_ini["mes"]] -= $dispersion_vales_ini["neto_subtotal"];
            $costo_cfdi[$dispersion_vales_ini["mes"]] -= $dispersion_vales_ini["neto_subtotal"];
            $diferencia[$dispersion_vales_ini["mes"]] -= $dispersion_vales_ini["neto_subtotal"];
        }

        foreach($costos_cfdi_e_ini as $costo_cfdi_e_ini)
        {
            $costo_cfdi_e[$costo_cfdi_e_ini["mes"]] = $costo_cfdi_e_ini["cfdi_e_recibidos"];
            $neto_tipo_e[$costo_cfdi_e_ini["mes"]] = $costo_cfdi_e_ini["cfdi_e_recibidos"];
            $costo_cfdi[$costo_cfdi_e_ini["mes"]] -= $costo_cfdi_e_ini["cfdi_e_recibidos"];
            $diferencia[$costo_cfdi_e_ini["mes"]] -= $costo_cfdi_e_ini["cfdi_e_recibidos"];
        }

        foreach($relaciones_ejercicios_anteriores_ini as $relacion_ejercicios_anteriores_ini)
        {
            $relacion_ejercicios_anteriores[$relacion_ejercicios_anteriores_ini["mes"]] = $relacion_ejercicios_anteriores_ini["neto_subtotal"];
            $neto_tipo_e[$relacion_ejercicios_anteriores_ini["mes"]] -= $relacion_ejercicios_anteriores_ini["neto_subtotal"];
            $costo_cfdi[$relacion_ejercicios_anteriores_ini["mes"]] +=$relacion_ejercicios_anteriores_ini["neto_subtotal"];
            $diferencia[$relacion_ejercicios_anteriores_ini["mes"]] +=$relacion_ejercicios_anteriores_ini["neto_subtotal"];
        }

        foreach($relaciones_ejercicios_posteriores_ini as $relacion_ejercicios_posteriores_ini)
        {
            $relacion_ejercicios_posteriores[$relacion_ejercicios_posteriores_ini["mes"]] = $relacion_ejercicios_posteriores_ini["neto_subtotal"];
            $neto_tipo_e[$relacion_ejercicios_posteriores_ini["mes"]] -= $relacion_ejercicios_posteriores_ini["neto_subtotal"];
            $costo_cfdi[$relacion_ejercicios_posteriores_ini["mes"]] +=$relacion_ejercicios_posteriores_ini["neto_subtotal"];
            $diferencia[$relacion_ejercicios_posteriores_ini["mes"]] +=$relacion_ejercicios_posteriores_ini["neto_subtotal"];
        }

        foreach($costos_balanza_ini as $costo_balanza_ini)
        {
            $costo_balanza[$costo_balanza_ini["periodo"]] = $costo_balanza_ini["costo_bza"];
            $diferencia[$costo_balanza_ini["periodo"]] -= $costo_balanza_ini["costo_bza"];
        }

        $meses = CFDICompleto::getMeses();

        foreach($meses as $mes){
            $informe[] = [
                "mes"=>$mes["mes"]
                , "id_mes" => $mes["id"]
                , "costo_cfdi"=>key_exists($mes["id"], $costo_cfdi)? number_format($costo_cfdi[$mes["id"]],2):"-"
                , "costo_cfdi_i"=>key_exists($mes["id"], $costo_cfdi_i)? number_format($costo_cfdi_i[$mes["id"]],2):"-"
                , "costo_balanza"=>key_exists($mes["id"], $costo_balanza)? number_format($costo_balanza[$mes["id"]],2):"-"
                , "sustitucion_ejercicios_anteriores"=>key_exists($mes["id"], $sustitucion_ejercicios_anteriores)? number_format($sustitucion_ejercicios_anteriores[$mes["id"]],2):"-"
                , "compraventa_divisas"=>key_exists($mes["id"], $compraventa_divisas)? number_format($compraventa_divisas[$mes["id"]],2):"-"
                , "dispersion_vales"=>key_exists($mes["id"], $dispersion_vales)? number_format($dispersion_vales[$mes["id"]],2):"-"
                , "neto_tipo_i"=>key_exists($mes["id"], $neto_tipo_i)? number_format($neto_tipo_i[$mes["id"]],2):"-"
                , "costo_cfdi_e"=>key_exists($mes["id"], $costo_cfdi_e)? number_format($costo_cfdi_e[$mes["id"]],2):"-"
                , "relacion_ejercicios_anteriores"=>key_exists($mes["id"], $relacion_ejercicios_anteriores)? number_format($relacion_ejercicios_anteriores[$mes["id"]],2):"-"
                , "relacion_ejercicios_posteriores"=>key_exists($mes["id"], $relacion_ejercicios_posteriores)? number_format($relacion_ejercicios_posteriores[$mes["id"]],2):"-"
                , "neto_tipo_e"=>key_exists($mes["id"], $neto_tipo_e)? number_format($neto_tipo_e[$mes["id"]],2):"-"
                , "diferencia"=>key_exists($mes["id"], $diferencia)? number_format($diferencia[$mes["id"]],2):"-"

                , "costo_cfdi_sf"=>key_exists($mes["id"], $costo_cfdi)? $costo_cfdi[$mes["id"]]:"0"
                , "costo_cfdi_i_sf"=>key_exists($mes["id"], $costo_cfdi_i)? $costo_cfdi_i[$mes["id"]]:"0"
                , "costo_cfdi_e_sf"=>key_exists($mes["id"], $costo_cfdi_e)? $costo_cfdi_e[$mes["id"]]:"0"
                , "costo_balanza_sf"=>key_exists($mes["id"], $costo_balanza)? $costo_balanza[$mes["id"]]:"0"
                , "sustitucion_ejercicios_anteriores_sf"=>key_exists($mes["id"], $sustitucion_ejercicios_anteriores)? $sustitucion_ejercicios_anteriores[$mes["id"]]:"0"
                , "compraventa_divisas_sf"=>key_exists($mes["id"], $compraventa_divisas)? $compraventa_divisas[$mes["id"]]:"0"
                , "dispersion_vales_sf"=>key_exists($mes["id"], $dispersion_vales)? $dispersion_vales[$mes["id"]]:"0"
                , "relacion_ejercicios_anteriores_sf"=>key_exists($mes["id"], $relacion_ejercicios_anteriores)? $relacion_ejercicios_anteriores[$mes["id"]]:"0"
                , "relacion_ejercicios_posteriores_sf"=>key_exists($mes["id"], $relacion_ejercicios_posteriores)? $relacion_ejercicios_posteriores[$mes["id"]]:"0"
                , "neto_tipo_i_sf"=>key_exists($mes["id"], $neto_tipo_i)? $neto_tipo_i[$mes["id"]]:"0"
                , "neto_tipo_e_sf"=>key_exists($mes["id"], $neto_tipo_e)? $neto_tipo_e[$mes["id"]]:"0"
                , "diferencia_sf"=>key_exists($mes["id"], $diferencia)? $diferencia[$mes["id"]]:"0"
            ];
        }
        return $informe;
    }

    private static function getAliasBDD($id_empresa_sat)
    {
        $empresa_contpaq = Empresa::where("IdEmpresaSAT","=",$id_empresa_sat)->consolidadora()->first();
        if($empresa_contpaq){
            return $empresa_contpaq->AliasBDD;
        }else {
            return null;
        }
    }

    private static function getCostoBalanza($data)
    {
        $empresa_contpaq = Empresa::where("IdEmpresaSAT","=",$data["empresa_sat"])
            ->consolidadora()
            ->first();

        if($empresa_contpaq){
            Config::set('database.connections.cntpq_inf.database',$empresa_contpaq->AliasBDD);

            $cuentas = CuentaCosto::where("tipo_costo","=",1)->where("estatus","=",1)->
            where("base_datos_contpaq","=",$empresa_contpaq->AliasBDD)->pluck("codigo_cuenta")->toArray();

            $informe_qry = "
            SELECT Periodo as periodo, sum(Importe) as costo_bza from (
                SELECT Periodo,
                CASE
                    mp.TipoMovto WHEN 0 THEN Importe
                    WHEN 1 THEN Importe * -1
                END Importe
                from ".$empresa_contpaq->AliasBDD.".dbo.MovimientosPoliza mp
                join ".$empresa_contpaq->AliasBDD.".dbo.Cuentas ct on(ct.Id = mp.IdCuenta)
                where Ejercicio = ".$data["anio"]." and ct.Codigo
                in('".implode("','", $cuentas)."')
                and Periodo not in(13,14)
            ) AS qry
            GROUP by Periodo
            ";

            $informe = DB::connection("cntpq_inf")->select($informe_qry);
            $informe = array_map(function ($value) {
                return (array)$value;
            }, $informe);

            return $informe;

        }else {
            return null;
        }
    }

    private static function getSustitucionEjerciciosAnteriores($data)
    {
        $informe_qry = "
        select mes, sum(neto_subtotal) as neto_subtotal from (
SELECT
distinct
	cfd_sat.id_proveedor_sat,
	CASE
		WHEN cfd_sat.tipo_comprobante = 'E' THEN 2
		WHEN cfd_sat.tipo_comprobante = 'I' THEN 1
	END id_tipo_cfdi,
	cfd_sat.fecha,

		CASE
			WHEN cfd_sat.moneda != 'MXN'
			AND cfd_sat.tipo_cambio > 0 THEN
			CASE
				WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1) * cfd_sat.tipo_cambio
				WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
			END
			ELSE
			CASE
				WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1)
				WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0))
			END
		END AS neto_subtotal,
		month(fecha) as mes
	FROM
		SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat

	WHERE
		(((cfd_sat.cancelado = 0
			AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"].")
		AND cfd_sat.tipo_comprobante IN ('I'))
			) AND year(cfd_sat.fecha) = ".$data["anio"]."
			and cfd_sat.cfdi_relacionado is not null and cfd_sat.tipo_relacion=4
			and cfdi_relacionado not in(
				SELECT uuid from SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
				WHERE YEAR(fecha) = ".$data["anio"]."
			)
	) as qry
	GROUP by mes
        ";
        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getCompraVentaDivisas($data)
    {
        $informe_qry = "
        select mes, sum(neto_subtotal) as neto_subtotal from (
SELECT
cfd_sat.id,
    cfd_sat.numero_empresa AS numero_empresa,
    cfd_sat.id_proveedor_sat,
    CASE
        WHEN cfd_sat.tipo_comprobante = 'E' THEN 2
        WHEN cfd_sat.tipo_comprobante = 'I' THEN 1
    END id_tipo_cfdi,
    cfd_sat.fecha,
    cfd_sat.descuento AS descuento,
    cfd_sat.subtotal AS subtotal,
    CASE
        WHEN cfd_sat.moneda != 'MXN'
        AND cfd_sat.tipo_cambio > 0 THEN
        CASE
            WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1) * cfd_sat.tipo_cambio
            WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
        END
        ELSE
        CASE
            WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1)
            WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0))
        END
    END AS neto_subtotal
    , cfd_sat.id_empresa_sat,
		month(fecha) as mes

FROM
    SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
INNER JOIN

(select distinct id_cfd_sat from SEGURIDAD_ERP.Contabilidad.cfd_sat_conceptos  where clave_prod_serv in('84121600','84121603') ) as csc
on(csc.id_cfd_sat = cfd_sat.id)

	WHERE
		(((cfd_sat.cancelado = 0
			AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"].")
		AND cfd_sat.tipo_comprobante IN ('I','E'))
			) AND year(cfd_sat.fecha) = ".$data["anio"]."

	) as qry
	GROUP by mes
        ";
        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getDispersionVales($data)
    {
        $informe_qry = "
        select mes, sum(neto_subtotal) as neto_subtotal from (
SELECT
cfd_sat.id,
    cfd_sat.numero_empresa AS numero_empresa,
    cfd_sat.id_proveedor_sat,
    CASE
        WHEN cfd_sat.tipo_comprobante = 'E' THEN 2
        WHEN cfd_sat.tipo_comprobante = 'I' THEN 1
    END id_tipo_cfdi,
    cfd_sat.fecha,
    cfd_sat.descuento_xls AS descuento,
    cfd_sat.subtotal_xls AS subtotal,
     CASE
        WHEN cfd_sat.moneda != 'MXN'
        AND cfd_sat.tipo_cambio > 0 THEN
        CASE
            WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1) * cfd_sat.tipo_cambio
            WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
        END
        ELSE
        CASE
            WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1)
            WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0))
        END
    END AS neto_subtotal
    , cfd_sat.id_empresa_sat,
		month(fecha) as mes
FROM
    SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
INNER JOIN

(select distinct id_cfd_sat from SEGURIDAD_ERP.Contabilidad.cfd_sat_conceptos  where clave_prod_serv = '84141602') as csc
on(csc.id_cfd_sat = cfd_sat.id)

	WHERE
		(((cfd_sat.cancelado = 0
			AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"].")
		AND cfd_sat.tipo_comprobante IN ('E','I'))
			) AND year(cfd_sat.fecha) = ".$data["anio"]."

	) as qry
	GROUP by mes
        ";
        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getRelacionEjerciciosAnteriores($data)
    {
        $informe_qry = "
        select mes, sum(neto_subtotal) as neto_subtotal from (
SELECT
distinct
	cfd_sat.id_proveedor_sat,
	CASE
		WHEN cfd_sat.tipo_comprobante = 'E' THEN 2
		WHEN cfd_sat.tipo_comprobante = 'I' THEN 1
	END id_tipo_cfdi,
	cfd_sat.fecha,

		CASE
			WHEN cfd_sat.moneda != 'MXN'
			AND cfd_sat.tipo_cambio > 0 THEN
			CASE
				WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) *  cfd_sat.tipo_cambio
				WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
			END
			ELSE
			CASE
				WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0))
				WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0))
			END
		END AS neto_subtotal,
		month(fecha) as mes
	FROM
		SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat

	WHERE
		(((cfd_sat.cancelado = 0
			AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"].")
		AND cfd_sat.tipo_comprobante IN ('E'))
			) AND year(cfd_sat.fecha) = ".$data["anio"]."
			and cfd_sat.cfdi_relacionado is not null
			and cfdi_relacionado not in(
				SELECT uuid from SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
				WHERE YEAR(fecha) = ".$data["anio"]."
			)
	) as qry
	GROUP by mes
        ";
        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getRelacionEjerciciosPosteriores($data)
    {
        $informe_qry = "
        select mes, sum(neto_subtotal) as neto_subtotal from (
SELECT
distinct
	cfd_sat.id_proveedor_sat,
	CASE
		WHEN cfd_sat_posteriores.tipo_comprobante = 'E' THEN 2
		WHEN cfd_sat_posteriores.tipo_comprobante = 'I' THEN 1
	END id_tipo_cfdi,
	cfd_sat_posteriores.fecha,

		CASE
			WHEN cfd_sat_posteriores.moneda != 'MXN'
			AND cfd_sat_posteriores.tipo_cambio > 0 THEN
			CASE
				WHEN cfd_sat_posteriores.tipo_comprobante = 'E' THEN (cfd_sat_posteriores.subtotal - isnull(cfd_sat_posteriores.descuento,0)) *  cfd_sat_posteriores.tipo_cambio
				WHEN cfd_sat_posteriores.tipo_comprobante = 'I' THEN (cfd_sat_posteriores.subtotal - isnull(cfd_sat_posteriores.descuento,0)) * cfd_sat_posteriores.tipo_cambio
			END
			ELSE
			CASE
				WHEN cfd_sat_posteriores.tipo_comprobante = 'E' THEN (cfd_sat_posteriores.subtotal - isnull(cfd_sat_posteriores.descuento,0))
				WHEN cfd_sat_posteriores.tipo_comprobante = 'I' THEN (cfd_sat_posteriores.subtotal - isnull(cfd_sat_posteriores.descuento,0))
			END
		END AS neto_subtotal,
		month(cfd_sat.fecha) as mes
	FROM
		SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat join SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_posteriores
on(cfd_sat_posteriores.cfdi_relacionado = cfd_sat.uuid AND year(cfd_sat_posteriores.fecha) > ".$data["anio"].")
AND cfd_sat_posteriores.tipo_comprobante IN ('E')
	WHERE
		(((cfd_sat.cancelado = 0
			AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"].")
		AND cfd_sat.tipo_comprobante IN ('I'))
			) AND year(cfd_sat.fecha) = ".$data["anio"]."

	) as qry
	GROUP by mes
        ";
        //echo $informe_qry;
        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getCostoCFDI($data)
    {

        $informe_qry = "
        select mes,
               CatalogoMeses.NombreMes AS mes_txt,
               sum(neto_subtotal) as cfdi_recibidos  from(

    select distinct
        cfd_sat.id,
        month(cfd_sat.fecha) as mes,
        CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0)) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0))
            END
        END AS neto_subtotal

        FROM
            SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
        WHERE
            (((cfd_sat.cancelado = 0
                AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]." )
            AND cfd_sat.tipo_comprobante IN ('E', 'I'))
                )
                AND year(cfd_sat.fecha) = ".$data["anio"]."
        )as query INNER JOIN SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
          ON (query.mes = CatalogoMeses.MesID)

    GROUP by mes, CatalogoMeses.NombreMes ;
        ";


        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getCostoCFDII($data)
    {
        $informe_qry = "
        select mes,
               CatalogoMeses.NombreMes AS mes_txt,
               sum(neto_subtotal) as cfdi_i_recibidos  from(

    select distinct
        cfd_sat.id,
        month(cfd_sat.fecha) as mes,
        CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0)) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0))
            END
        END AS neto_subtotal

        FROM
            SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
        WHERE
            (((cfd_sat.cancelado = 0
                AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]." )
            AND cfd_sat.tipo_comprobante IN ('I'))
                )
                AND year(cfd_sat.fecha) = ".$data["anio"]."
        )as query INNER JOIN SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
          ON (query.mes = CatalogoMeses.MesID)

    GROUP by mes, CatalogoMeses.NombreMes ;
        ";


        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getCostoCFDIE($data)
    {
        $informe_qry = "
        select mes,
               CatalogoMeses.NombreMes AS mes_txt,
               sum(neto_subtotal) as cfdi_e_recibidos  from(

    select distinct
        cfd_sat.id,
        month(cfd_sat.fecha) as mes,
        CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0))  * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0))
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0))
            END
        END AS neto_subtotal

        FROM
            SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
        WHERE
            (((cfd_sat.cancelado = 0
                AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]." )
            AND cfd_sat.tipo_comprobante IN ('E'))
                )
                AND year(cfd_sat.fecha) = ".$data["anio"]."
        )as query INNER JOIN SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
          ON (query.mes = CatalogoMeses.MesID)

    GROUP by mes, CatalogoMeses.NombreMes ;
        ";


        $informe = DB::connection("seguridad")->select($informe_qry);
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        return $informe;
    }

    private static function getMeses()
    {
        $query = "
        SELECT
        CatalogoMeses.NombreCorto AS mes_txt,
        CatalogoMeses.MesID as id
        FROM SEGURIDAD_ERP.Reportes.CatalogoMeses CatalogoMeses
        ";

        $meses = DB::select($query);
        $meses = array_map(function ($value) {
            return (array)$value;
        }, $meses);
        return $meses;
    }

    public static function getListaCFDIEjercicioPosterior($data){

       $qry = "
      SELECT distinct cfd_sat.*,
            CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0)) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0))
            END
        END AS subtotal_a_sumar,

        CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal )
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal )
            END
        END AS subtotal_mxn,


        CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.descuento ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.descuento )
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.descuento ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.descuento )
            END
        END AS descuento_mxn,

       '' AS id_reemplazado,
       '' AS serie_reemplazado,
       '' AS folio_reemplazado,
       '' AS fecha_reemplazado,

       '' AS id_reemplaza,
       '' AS fecha_reemplaza,
       '' AS serie_reemplaza,
       '' AS folio_reemplaza,
       configuracion_obra.nombre AS obra_sao,
       informe_sat_lista_empresa.descripcion as empresa_contpaq,
cfd_sat_i.id as id_relacionado,
cfd_sat_i.serie + ' ' + cfd_sat_i.folio as cfdi_relacionado,
cfd_sat_i.fecha as fecha_relacionado
  FROM (((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
          LEFT OUTER JOIN
          SEGURIDAD_ERP.Finanzas.repositorio_facturas repositorio_facturas
             ON (cfd_sat.uuid = repositorio_facturas.uuid))
         )
        )
       LEFT OUTER JOIN
       SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
          ON     (repositorio_facturas.id_proyecto =
              configuracion_obra.id_proyecto)
          AND (repositorio_facturas.id_obra = configuracion_obra.id_obra)
      LEFT OUTER JOIN
      SEGURIDAD_ERP.Contabilidad.polizas_cfdi on(polizas_cfdi.uuid = cfd_sat.uuid)
      LEFT OUTER JOIN
      SEGURIDAD_ERP.Contabilidad.informe_sat_lista_empresa on(informe_sat_lista_empresa.numero = polizas_cfdi.numero_empresa)


      JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat_i
      on(cfd_sat_i.uuid = cfd_sat.cfdi_relacionado)
    WHERE month(cfd_sat_i.fecha) = ".$data["mes"]."
      AND  year(cfd_sat_i.fecha) = ".$data["anio"]."
      AND cfd_sat_i.cancelado = 0
      AND cfd_sat.cancelado = 0
        AND cfd_sat.tipo_comprobante in('E')
        AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]."
        AND year(cfd_sat.fecha) > ".$data["anio"]."
        order by cfd_sat.id";


        $informe = DB::connection("seguridad")->select($qry);

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        $i = 0;
        foreach($informe as $partida_informe)
        {
            if($i>0) {
                if ($partida_informe["id"] != $informe[$i - 1]["id"]){
                    $total += $partida_informe["subtotal_a_sumar"];
                }
            } else {
                $total += $partida_informe["subtotal_a_sumar"];
            }
            $i++;
        }

        return ["informe" => $informe, "total_format"=>"$".number_format($total,2), "total"=>$total];

    }

    public static function getListaCFDI($data){
        $qry = "
      SELECT distinct cfd_sat.*,
            CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * (-1) * cfd_sat.tipo_cambio
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull(cfd_sat.descuento,0)) * cfd_sat.tipo_cambio
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0)) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal - isnull( cfd_sat.descuento,0))
            END
        END AS subtotal_a_sumar,

        CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal )
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.subtotal ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.subtotal )
            END
        END AS subtotal_mxn,


CASE
            WHEN cfd_sat.moneda != 'MXN'
            AND cfd_sat.tipo_cambio > 0 THEN
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.descuento ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.descuento )
            END
            ELSE
            CASE
                WHEN cfd_sat.tipo_comprobante = 'E' THEN (cfd_sat.descuento ) * (-1)
                WHEN cfd_sat.tipo_comprobante = 'I' THEN (cfd_sat.descuento )
            END
        END AS descuento_mxn,

       cfd_sat_1.id AS id_reemplazado,
       cfd_sat_1.serie AS serie_reemplazado,
       cfd_sat_1.folio AS folio_reemplazado,
       cfd_sat_1.fecha AS fecha_reemplazado,

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
    WHERE month(cfd_sat.fecha) = ".$data["mes"]."
      AND  year(cfd_sat.fecha) = ".$data["anio"]."
      AND cfd_sat.cancelado = 0
        AND cfd_sat.tipo_comprobante in('I','E')
        AND cfd_sat.id_empresa_sat = ".$data["empresa_sat"]." order by cfd_sat.id";

        $informe = DB::connection("seguridad")->select($qry);

        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);

        $total = 0;
        $i = 0;
        foreach($informe as $partida_informe)
        {
            if($i>0) {
                if ($partida_informe["id"] != $informe[$i - 1]["id"]){
                    $total += $partida_informe["subtotal_a_sumar"];
                }
            } else {
                $total += $partida_informe["subtotal_a_sumar"];
            }
            $i++;
        }

        return ["informe" => $informe, "total"=>"$".number_format($total,2)];
    }
}
