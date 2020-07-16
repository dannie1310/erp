<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEstadosEfos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;

class EFOS extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.efos';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'rfc',
        'razon_social',
        'estado',
        'id_proveedor_sat',
        'id_procesamiento_registro',
        'id_procesamiento_actualizacion',
        'id_carga_cfd'
    ];

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function efo()
    {
        return $this->belongsTo(CtgEfos::class,"rfc","rfc");
    }

    public function cambios()
    {
        return $this->hasMany(EFOSCambio::class, "id_efo", "id");
    }

    public function estadoEFOS()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado', 'id');
    }

    public function CFDAutocorreccion()
    {
        return $this->hasMany(CFDAutocorreccion::class, 'id_proveedor_sat', 'id_proveedor_sat');
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
        $informe["informe"][] = EFOS::getPartidasInformeDefinitivos();
        $informe["informe"][] = EFOS::getPartidasInformePresuntos();
        $informe["informe"][] = EFOS::getPartidasInformeAutocorregidos();
        $informe["fechas"] = EFOS::getFechasInforme();
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
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       ListaEmpresasSAT.nombre_corto AS empresa,
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
 WHERE (efos.estado = 2) and cfd_sat.tipo_comprobante != 'P' and ctg_efos.estado_registro = 1
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto,
         Subquery.fecha_presunto_maxima
ORDER BY Subquery.fecha_presunto_maxima DESC,
         empresa ASC,
         ctg_efos.fecha_presunto DESC")
            ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOS::setSubtotalesPartidas($informe, "PRESUNTOS");
        return $informe;
    }

    private static function getPartidasInformeAutocorregidos(){
        $informe = DB::select("
        SELECT 'Corregido' AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       ListaEmpresasSAT.nombre_corto AS empresa,
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
        (SELECT cfd_sat.id
           FROM SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones cfd_sat_autocorrecciones
                INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                   ON (cfd_sat_autocorrecciones.id_cfd_sat = cfd_sat.id))
        Subquery
           ON (cfd_sat.id = Subquery.id))
       INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
          ON (ctg_efos.rfc = efos.rfc)
 WHERE (efos.estado = 0) and cfd_sat.tipo_comprobante != 'P' and ctg_efos.estado_registro = 1
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto
ORDER BY 8 DESC
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
        $informe = EFOS::setSubtotalesPartidas($informe, "CORREGIDOS");
        return $informe;
    }

    private static function getPartidasInformeDefinitivos(){
        $informe = DB::select("
        SELECT ctg_estados_efos.descripcion AS estatus,
       efos.rfc,
       efos.razon_social,
       CONVERT(varchar,ctg_efos.fecha_presunto,103) as fecha_presunto,
       CONVERT(varchar,ctg_efos.fecha_definitivo,103)  as fecha_definitivo,
       ListaEmpresasSAT.nombre_corto AS empresa,
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
  FROM (((((SEGURIDAD_ERP.Fiscal.efos efos
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
                 MAX (ctg_efos.fecha_definitivo) AS fecha_devinitivo_maxima
            FROM ((SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                   INNER JOIN
                   SEGURIDAD_ERP.Contabilidad.ListaEmpresasSAT ListaEmpresasSAT
                      ON (cfd_sat.id_empresa_sat = ListaEmpresasSAT.id))
                  INNER JOIN SEGURIDAD_ERP.Fiscal.efos efos
                     ON (cfd_sat.rfc_emisor = efos.rfc))
                 INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
                    ON (efos.razon_social = ctg_efos.razon_social)
           WHERE ctg_efos.estado = 0 and cfd_sat.tipo_comprobante != 'P' and ctg_efos.estado_registro = 1
          GROUP BY ListaEmpresasSAT.id, ListaEmpresasSAT.nombre_corto)
         Subquery
            ON (ListaEmpresasSAT.id = Subquery.id))
        LEFT OUTER JOIN
        (SELECT cfd_sat.id
           FROM SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones cfd_sat_autocorrecciones
                INNER JOIN SEGURIDAD_ERP.Contabilidad.cfd_sat cfd_sat
                   ON (cfd_sat_autocorrecciones.id_cfd_sat = cfd_sat.id))
        cfd_autocorregidos
           ON (cfd_sat.id = cfd_autocorregidos.id))
       INNER JOIN SEGURIDAD_ERP.Fiscal.ctg_efos ctg_efos
          ON (ctg_efos.rfc = efos.rfc)
 WHERE (efos.estado = 0) AND (cfd_autocorregidos.id IS NULL) and ctg_efos.estado_registro = 1
GROUP BY ctg_estados_efos.descripcion,
         efos.rfc,
         efos.razon_social,
         ctg_efos.fecha_definitivo,
         ListaEmpresasSAT.nombre_corto,
         ctg_efos.fecha_presunto,
         Subquery.fecha_devinitivo_maxima
ORDER BY Subquery.fecha_devinitivo_maxima DESC,
         empresa ASC,
         ctg_efos.fecha_definitivo DESC
        ")
        ;
        $informe = array_map(function ($value) {
            return (array)$value;
        }, $informe);
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
                    //if($acumulador_partidas_empresa>1){
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
                    //}
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

        $partidas_completas[$i]["contador"] = $contador_partidas_empresa-1;
        $partidas_completas[$i]["acumulador"] = $acumulador_partidas_empresa;
        $partidas_completas[$i]["etiqueta"] = "SUBTOTAL ".$tipo." ".$partidas[count($partidas)-1]["empresa"];
        $partidas_completas[$i]["contador_cfdi"] = $contador_cfdi;
        $partidas_completas[$i]["importe"] = $importe_cfdi;
        $partidas_completas[$i]["importe_format"] = '$ '.number_format($importe_cfdi,2,".",",");
        $partidas_completas[$i]["tipo"] = "subtotal";
        $partidas_completas[$i]["bg_color_hex"] = "#d5d5d5";
        $partidas_completas[$i]["bg_color_rgb"] = [213,213,213];
        $partidas_completas[$i]["color_hex"] = "#000";
        $partidas_completas[$i]["color_rgb"] = [0,0,0];
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
        return $partidas_completas;
    }

    public static function actualizaEFOS(ProcesamientoListaEfos $procesamiento = null, CargaCFDSAT $carga = null) {
        if($procesamiento){
            $existentes = CtgEfos::esProveedor()->registrado()->get();
            foreach($existentes as $existente){
                $efo = EFOS::where("rfc",$existente->rfc)->first();
                $efo->update([
                    "estado"=>$existente->estado,
                    "id_procesamiento_actualizacion" => $procesamiento->id
                ]);
            }
        }

        $nuevos = CtgEfos::esProveedor()->noRegistrado()->esPresuntoDefinitivo()->get();
        foreach($nuevos as $nuevo){
            if($procesamiento) {
                EFOS::create([
                    "rfc" => $nuevo->rfc,
                    "razon_social" => $nuevo->razon_social,
                    "estado" => $nuevo->estado,
                    "id_proveedor_sat" => $nuevo->proveedor->id,
                    "id_procesamiento_registro" => $procesamiento->id
                ]);
            } else if($carga){
                EFOS::create([
                    "rfc" => $nuevo->rfc,
                    "razon_social" => $nuevo->razon_social,
                    "estado" => $nuevo->estado,
                    "id_proveedor_sat" => $nuevo->proveedor->id,
                    "id_carga_cfd" => $carga->id
                ]);
            }
        }
        if($procesamiento){
            if($procesamiento->cambios){

            }
        }
        if($carga){
            if($carga->cambios){

            }
        }
    }
}