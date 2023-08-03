<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CTPQ\DocumentMetadata\Comprobante;
use App\Models\CTPQ\Parametro;
use App\Models\SEGURIDAD_ERP\catCFDI\TipoComprobante;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Models\SEGURIDAD_ERP\Fiscal\VwCFDSATPendientesREP;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgEstadoCFD;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Utils\CFD;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;
use Exception;

class CFDSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat';
    public $timestamps = false;
    protected $fillable =[
        "version"
        ,"rfc_emisor"
        ,"rfc_receptor"
        ,"xml_file"
        ,"uuid"
        ,"serie"
        ,"folio"
        ,"fecha"
        ,"total_impuestos_trasladados"
        ,"total_impuestos_retenidos"
        ,"tasa_iva"
        ,"importe_iva"
        ,"descuento"
        ,"subtotal"
        ,"total"
        ,"id_empresa_sat"
        ,"id_proveedor_sat"
        ,"moneda"
        ,"id_carga_cfd_sat"
        ,"tipo_comprobante"
        ,"estado"
        ,"estado_txt"
        ,"fecha_cancelacion"
        ,"tipo_cambio"
        ,"id_solicitud_recepcion"
        ,"ultima_verificacion"
        ,"no_verificable"
        ,"cancelado"
        ,"metodo_pago"
        ,"tipo_relacion"
        ,"cfdi_relacionado"
        ,"forma_pago"
        ,"id_tipo_transaccion"
        ,"conceptos_txt"
        ,"moneda_pago"
        ,"monto_pago"
        ,"fecha_pago"
        ,"forma_pago_p"
    ];

    protected $dates =["fecha", "fecha_cancelacion","ultima_verificacion"];
    //protected $dateFormat = 'Y-m-d H:i:s';

    public function carga()
    {
        return $this->belongsTo(CargaCFDSAT::class, 'id_carga_cfd_sat', 'id');
    }

    public function solicitudRecepcion()
    {
        return $this->belongsTo(SolicitudRecepcionCFDI::class, "id_solicitud_recepcion", "id");
    }

    public function conceptos()
    {
        return $this->hasMany(CFDSATConceptos::class, 'id_cfd_sat', 'id');
    }

    public function traslados()
    {
        return $this->hasMany(CFDSATTraslados::class, 'id_cfd_sat', 'id');
    }

    public function retenciones()
    {
        return $this->hasMany(CFDSATRetenciones::class, 'id_cfd_sat', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function proveedorHermes()
    {
        return $this->belongsTo(ProveedorREP::class, 'id_proveedor_sat', 'id')
            ->where("es_empresa_hermes","=",1);
    }

    public function proveedorNoHermes()
    {
        return $this->belongsTo(ProveedorREP::class, 'id_proveedor_sat', 'id')
            ->where("es_empresa_hermes","=",0);
    }

    public function proveedorConContactos()
    {
        return $this->belongsTo(ProveedorREP::class, 'id_proveedor_sat', 'id')
            ->where("cantidad_contactos",">",0);
    }

    public function proveedorSinContactos()
    {
        return $this->belongsTo(ProveedorREP::class, 'id_proveedor_sat', 'id')
            ->where("cantidad_contactos","=",0);
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaSAT::class, 'id_empresa_sat', 'id');
    }

    public function efo()
    {
        return $this->belongsTo(EFOS::class,"rfc_emisor","rfc");
    }

    public function asociado()
    {
        return $this->belongsTo(CFDSAT::class,"cfdi_relacionado","uuid");
    }

    public function autocorreccion()
    {
        return $this->hasOne(CFDAutocorreccion::class, "id_cfd_sat", "id");
    }

    public function ctgEstado()
    {
        return $this->belongsTo(CtgEstadoCFD::class, 'estado', 'id');
    }

    public function tipoTransaccion()
    {
        return $this->belongsTo(CtgTipoTransaccion::class, "id_tipo_transaccion", "id");
    }

    public function tipoComprobante()
    {
        return $this->belongsTo(TipoComprobante::class, "tipo_comprobante", "tipo_comprobante");
    }

    public function facturaRepositorio()
    {
        return $this->hasOne(FacturaRepositorio::class, "uuid", "uuid");
    }

    public function polizaCFDI()
    {
        return $this->hasOne(PolizaCFDI::class, "uuid", "uuid");
    }

    public function documentosPagados()
    {
        return $this->hasMany(CFDSATDocumentosPagados::class, "id_cfdi_pago", "id");
    }

    public function pagos()
    {
        return $this->hasMany(CFDSATDocumentosPagados::class, "id_cfdi_pagado", "id");
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, "id_cfdi", "id");
    }

    public function vwPendienteREP()
    {
        return $this->hasOne(VwCFDSATPendientesREP::class,"id_cfdi", "id");
    }

    public function logsADD()
    {
        return $this->hasMany(CFDISATLogADD::class, "id_cfdi_sat", "id");
    }

    public function comprobanteADD()
    {
        return $this->hasOne(Comprobante::class,"UUID","uuid");
    }

    public function scopeDeEFO($query)
    {
        return $query->whereHas("efo");
    }

    public function scopeNoAutocorregidos($query)
    {
        return $query->doesnthave("autocorreccion");
    }

    public function scopeDefinitivo($query)
    {
        return $query->where('estado', '=', 0);
    }

    public function scopeExceptoTipo($query, $tipo)
    {
        return $query->where('tipo_comprobante', '!=', $tipo);
    }

    public function scopeParaProyecto($query){
        $rfc_contexto = Obra::find(Context::getIdObra())->rfc;
        $proyecto_contexto = Proyecto::where("base_datos","=",Context::getDatabase())->first()->id;
        return $query->where("cfd_sat.rfc_receptor","=", $rfc_contexto)
            ->join(Context::getDatabase().".dbo.empresas","rfc_emisor","=","empresas.rfc")
            ->leftJoin("Finanzas.repositorio_facturas","repositorio_facturas.uuid","=","cfd_sat.uuid")
            ->whereNull("repositorio_facturas.uuid")
            ->orWhere("repositorio_facturas.id_proyecto","=", $proyecto_contexto)->where("repositorio_facturas.id_obra","=",Context::getIdObra())
            ->select("cfd_sat.*")->distinct()
            ;
    }

    public function scopePorProveedor($query, $id_proveedor)
    {
        return $query->where('id_proveedor_sat', '=', $id_proveedor);
    }

    public function scopeEnSolicitud($query)
    {
        return $query->whereHas('solicitudRecepcion');
    }

    public function scopeBancoGlobal($query)
    {
        return $query->where('id_ctg_bancos', '!=', null);
    }

    public function scopePendienteProcesamientoDoctosPagados($query)
    {
        return $query->where('tipo_comprobante', '=', 'P')
            ->where("cancelado","=",0)
            ->whereDoesntHave("documentosPagados");
    }

    public function scopeRepPendiente($query)
    {
        return $query->join("Fiscal.etl_cfdi_sat_rep_pendientes","etl_cfdi_sat_rep_pendientes.id_cfdi","=","cfd_sat.id")
            ->where('tipo_comprobante', '=', 'I')
            ->where("cancelado","=",0)
            ->where("cfd_sat.metodo_pago","=","PPD")
            ;
    }


    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getFechaCortaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public static function getFechaUltimoCFDTxt()
    {
        $ultimo_cfd = CFDSAT::orderBy("fecha","desc")->first();
        $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
        $mes = $meses[($ultimo_cfd->fecha->format('n')) - 1];
        $fecha = "CFDI cargados al ".$ultimo_cfd->fecha->format("d")." de ".$mes. " de ".$ultimo_cfd->fecha->format("Y");
        return $fecha;
    }

    public static function getFechaUltimoREP()
    {
        $ultimo_cfdi_rep = CFDSAT::where("tipo_comprobante","=","P")
            ->where("cancelado","=","0")
            ->orderBy("fecha","desc")->first();

        return $ultimo_cfdi_rep->fecha->format("d/m/Y");
    }

    public static function getFechaUltimaCancelacion()
    {
        $ultimo_cfdi_rep = CFDSAT::where("tipo_comprobante","=","P")
            ->where("cancelado","=","0")
            ->orderBy("fecha","desc")->first();

        return $ultimo_cfdi_rep->fecha->format("d/m/Y");
    }

    public function getTotalMxnAttribute()
    {
        if($this->moneda != "MXN"){
            if($this->tipo_cambio>0){
                return $this->total * $this->tipo_cambio;
            }else{
                return $this->total;
            }

        }else{
            return $this->total;
        }
    }

    public function getImporteIvaMxnAttribute()
    {
        if($this->moneda != "MXN"){
            if($this->tipo_cambio>0){
                return $this->importe_iva * $this->tipo_cambio;
            }else{
                return $this->importe_iva;
            }

        }else{
            return $this->importe_iva;
        }
    }

    public function getTotalMxnFormatAttribute()
    {
        return '$ ' . number_format(($this->total_mxn),2);
    }

    public function getTotalFormatAttribute()
    {
        return '$ ' . number_format(($this->total),2);
    }

    public function getSubtotalFormatAttribute()
    {
        return '$' . number_format(($this->subtotal),2);
    }

    public function getDescuentoFormatAttribute()
    {
        return '$' . number_format(($this->descuento),2);
    }

    public function getTotalImpuestosRetenidosFormatAttribute()
    {
        return '$' . number_format(($this->total_impuestos_retenidos),2);
    }

    public function getTotalImpuestosTrasladadosFormatAttribute()
    {
        return '$' . number_format(($this->total_impuestos_trasladados),2);
    }

    public function getReferenciaAttribute()
    {
        return $this->serie .' '. $this->folio;
    }

    public function getXMLAttribute()
    {
        $xml = DB::table("Contabilidad.cfd_sat")
            ->select(DB::raw("'data:text/xml;base64,' + CONVERT(varchar(MAX), xml_file ,0) as xml"))
            ->where("id",$this->id)
            ->first();
        return $xml->xml;
    }

    public function getConceptoTxtAttribute()
    {
        $concepto_arr = [];
        foreach ($this->conceptos as $concepto)
        {
            $concepto_arr[]= $concepto->descripcion;
        }
        return implode(" | ",$concepto_arr);
    }

    public function getMontoPendienteRepVwAttribute()
    {
        if($this->vwPendienteREP){
            return $this->vwPendienteREP->pendiente_pago;
        }else{
            return $this->total;
        }
    }

    public function getMontoPendienteRepVwMxnAttribute()
    {
        if($this->moneda != "MXN"){
            if($this->tipo_cambio>0){
                return $this->monto_pendiente_rep_vw * $this->tipo_cambio;
            }else{
                return $this->monto_pendiente_rep_vw;
            }

        }else{
            return $this->monto_pendiente_rep_vw;
        }
    }

    public function getMontoPendienteRepVwFormatAttribute()
    {
        return '$ ' . number_format(($this->monto_pendiente_rep_vw),2);
    }

    public function getCantidadPagosAttribute()
    {
        if($this->vwPendienteREP){
            return $this->vwPendienteREP->cantidad_pagos;
        }else{
            return 0;
        }
    }

    public function getEstadoColorAttribute()
    {
        switch ($this->cancelado) {
            case 0:
                return '#00a65a';
                break;

            case 1:
                return '#f32c12';
                break;
        }
    }

    public function getTipoDescripcionAttribute()
    {
        return $this->tipo_comprobante === 'I' ? 'Ingreso' : ($this->tipo_comprobante == 'E' ? 'Egreso' : 'Pago');
    }

    public function getFechaEmisionFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/M/Y");
    }

    /*
     * public function comprobante()
    {
        $configuracion_obra = ConfiguracionObra::withoutGlobalScopes()
            ->where("id_proyecto", "=", $this->id_proyecto)
            ->where("id_obra", "=", $this->id_obra)->first();
        $proyecto = Proyecto::find($configuracion_obra->id_proyecto);

        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $proyecto->base_datos);
        $obra = Obra::find($configuracion_obra->id_obra);

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
        $base =  Parametro::find(1);

        DB::purge('cntpqdm');
        Config::set('database.connections.cntpqdm.database', 'document_'.$base->GuidDSL.'_metadata');
        return $this->hasOne(Comprobante::class,"UUID","uuid");
    }
     * */

    public function getTieneComprobanteAddAttribute()
    {
        if($this->comprobanteADD)
        {
            return true;
        }else{
            return false;
        }
    }

    public function registrar($data)
    {
        $factura = null;
        try {
            DB::connection('seguridad')->beginTransaction();

            $cfd = $this->create($data);
            $conceptos_arr = [];
            if(key_exists("conceptos",$data)){
                foreach($data["conceptos"] as $concepto){
                    $conceptos_arr[] = $concepto["descripcion"];
                    $conceptoObj = $cfd->conceptos()->create($concepto);
                    if(key_exists("traslados",$concepto)){
                        foreach($concepto["traslados"] as $traslado){
                            $conceptoObj->traslados()->create($traslado);
                        }
                    }
                    if(key_exists("retenciones",$concepto)){
                        foreach($concepto["retenciones"] as $retencion){
                            $conceptoObj->retenciones()->create($retencion);
                        }
                    }
                }
            }

            $cfd->conceptos_txt = implode(" | ",$conceptos_arr);
            $cfd->save();

            if(key_exists("traslados",$data)){
                foreach($data["traslados"] as $traslado){
                    $cfd->traslados()->create($traslado);
                }
            }

            if(key_exists("retenciones",$data)){
                foreach($data["retenciones"] as $retencion){
                    $cfd->retenciones()->create($retencion);
                }
            }

            if(key_exists("documentos_pagados",$data)){
                foreach($data["documentos_pagados"] as $documento_pagado){
                    $cfdi_pagado = CFDSAT::where("uuid", $documento_pagado["uuid"])->first();
                    if($cfdi_pagado){
                        $documento_pagado["id_cfdi_pagado"] = $cfdi_pagado->id;
                    }
                    $cfd->documentosPagados()->create($documento_pagado);
                }
            }
            DB::connection('seguridad')->commit();
            return $cfd;

        } catch (\Exception $e) {
            dd($e->getMessage(),$data);
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function complementarConceptosTxt()
    {
        if(count($this->conceptos) > 0)
        {
            $concepto_arr = [];
            foreach ($this->conceptos as $concepto)
            {
                $concepto_arr[]= $concepto->descripcion;
            }
            $this->conceptos_txt = implode(" | ",$concepto_arr);
            $this->save();
        }
    }

    public function complementarDatos()
    {
        $cfd = new CFD($this->xml);
        $data = $cfd->getArregloFactura();
        $this->subtotal = $data["subtotal"];
        $this->descuento = $data["descuento"];
        $this->metodo_pago = $data["metodo_pago"];
        $this->tipo_cambio = $data["tipo_cambio"];
        $this->total_impuestos_retenidos = $data["total_impuestos_retenidos"];
        $this->total_impuestos_trasladados = $data["total_impuestos_trasladados"];

        if($data["tipo_relacion"]>0){
            $this->tipo_relacion = $data["tipo_relacion"];
            $this->cfdi_relacionado = $data["cfdi_relacionado"];
        }else{
            $this->tipo_relacion = null;
            $this->cfdi_relacionado = null;
        }

        $this->save();

        if(key_exists("conceptos",$data)){
            $this->conceptos()->update(["estado"=>0]);
            foreach($data["conceptos"] as $concepto){
                $conceptoObj = $this->conceptos()->create($concepto);
                if(key_exists("traslados",$concepto)){
                    foreach($concepto["traslados"] as $traslado){
                        $conceptoObj->traslados()->create($traslado);
                    }
                }
                if(key_exists("retenciones",$concepto)){
                    foreach($concepto["retenciones"] as $retencion){
                        $conceptoObj->retenciones()->create($retencion);
                    }
                }
            }
        }
        if(key_exists("traslados",$data)){
            $this->traslados()->update(["estado"=>0]);
            foreach($data["traslados"] as $traslado){
                $this->traslados()->create($traslado);
            }
        }
        if(key_exists("retenciones",$data)){
            $this->retenciones()->update(["estado"=>0]);
            foreach($data["retenciones"] as $retencion){
                $this->retenciones()->create($retencion);
            }
        }


        if(key_exists("documentos_pagados",$data)){
            $this->documentosPagados()->delete();
            foreach($data["documentos_pagados"] as $documento_pagado){
                $cfdi_pagado = CFDSAT::where("uuid", $documento_pagado["uuid"])->first();
                if($cfdi_pagado){
                    $documento_pagado["id_cfdi_pagado"] = $cfdi_pagado->id;
                }
                $this->documentosPagados()->create($documento_pagado);
            }
        }
    }

    public function generaDocumentos()
    {
        if($this->id_tipo_transaccion>0)
        {
            $tiposArchivo = $this->tipoTransaccion->tiposArchivo;
            foreach($tiposArchivo as $tipoArchivo){
                $archivo["id_tipo_archivo"] = $tipoArchivo->id_tipo_archivo;
                $archivo["obligatorio"] = $tipoArchivo->obligatorio;
                try{
                    $this->archivos()->create($archivo);
                }catch (\Exception $e){

                }
            }
        }
    }

    public function actualizaObligatoriedadDocumentos()
    {
        if($this->id_tipo_transaccion>0)
        {
            $idTiposArchivoObligatorios = $this->tipoTransaccion->tiposArchivo->where("obligatorio","=","1")->pluck("id_tipo_archivo")->toArray();
            foreach($this->archivos as $archivo){
                if(!in_array($archivo->id_tipo_archivo, $idTiposArchivoObligatorios)){
                    $archivo->obligatorio = 0;
                    $archivo->save();
                }
            }
        }
    }

    public function eliminaDocumentos()
    {
        $archivos = $this->archivos;
        foreach($archivos as $archivo)
        {
            if(!$archivo->hashfile){
                $archivo->delete();
            }
        }
    }

    public function validaVigencia()
    {
        try{
            $cfd = new CFD($this->xml);
        } catch (\Exception $e){
            $this->no_verificable =  1;
            $this->save();
        }

        $vigente = $cfd->validaVigente();

        if(!$vigente)
        {
            $this->cancelado = 1;
            $this->fecha_cancelacion =  date('Y-m-d H:i:s.u');
            $this->ultima_verificacion =  date('Y-m-d H:i:s.u');
            $this->save();
        } else{
            $this->ultima_verificacion =  date('Y-m-d H:i:s.u');
            $this->save();
        }
    }

    public function guardarXmlEnADD(Empresa $empresa){

        $xml = "data:text/xml;base64," . $this->xml_file;
        $cfd = new CFD($xml);

        $xml_fuente = $cfd->archivo_xml;
        $xml_array = $cfd->arreglo_factura;
        $logs = [];

        if(in_array($cfd->arreglo_factura['tipo_comprobante'], ["I", "E", "P"])) {

            $xml_split = explode('base64,', $xml_fuente);
            $xml = base64_decode($xml_split[1]);

            $logs[] = ["tipo" => 0, "descripcion" => "Inicia"];
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            //try {
                $parametros = Parametro::first();
            /*} catch (Exception $e) {
                $logs[] = ["tipo" => -1, "descripcion" => "Error de lectura a la base de datos: " . Config::get('database.connections.cntpq.database') . "."];
            }*/

            //try {
                $arreglo_bbdd = $this->existDb($parametros->GuidDSL);
                if ($arreglo_bbdd == false) {
                    $logs[] = ["tipo" => -1, "descripcion" => "Error existDb"];
                }
            /*} catch (Exception $e) {
                $logs[] = ["tipo" => -1, "descripcion" => "Error existDb catch: " . $e->getMessage()];
            }*/

            //try {
                $val_insercionCertificado = $this->insUpdCertificate($xml_array['certificado'], $xml_array['no_certificado'], $xml_array['emisor']['rfc'], $xml_array['emisor']['nombre']);
                if (!$val_insercionCertificado) {
                    $logs[] = ["tipo" => -1, "descripcion" => "Error insUpdCertificate"];
                }
            /*} catch (Exception $e) {
                $logs[] = ["tipo" => -1, "descripcion" => "Error insUpdCertificate catch: " . $e->getMessage()];
            }*/
            $duplicado = false;
            //try {
                if ($duplicado = $this->buscarCfdiDuplicado($arreglo_bbdd[0]['NameDB'], $xml_array['uuid'])) {
                    $logs[] = ["tipo" => 1, "descripcion" => "CFDI ya existente en ADD"];
                }
            /*} catch (Exception $e) {
                $logs[] = ["tipo" => -1, "descripcion" => "Error buscarCfdiDuplicado catch: " . $e->getMessage()];
            }*/

            if (!$duplicado) {
                $guid_doc_metadata = Uuid::generate()->string;

                //try {
                    $va_insert_xml = $this->spInsUpdDocument($xml, $arreglo_bbdd[0]['NameDB'], $arreglo_bbdd[1]['NameDB'], $arreglo_bbdd[3]['NameDB'], $arreglo_bbdd[2]['NameDB'], $guid_doc_metadata, $xml_array['fecha_hora'], $xml_array['emisor']['rfc'], $xml_array['folio']);
                    if (!$va_insert_xml) {
                        $logs[] = ["tipo" => -1, "descripcion" => "Error spInsUpdDocument"];
                    } else {
                        $logs[] = ["tipo" => 1, "descripcion" => "Envío éxitoso, comprobante con GUID: " . $guid_doc_metadata . " en base de datos: " . Config::get('database.connections.cntpqdm.database')];
                    }
                /*} catch (Exception $e) {
                    $logs[] = ["tipo" => -1, "descripcion" => "Error spInsUpdDocument catch: " . $e->getMessage()];
                    dd($xml, $arreglo_bbdd[0]['NameDB'], $arreglo_bbdd[1]['NameDB'], $arreglo_bbdd[3]['NameDB'], $arreglo_bbdd[2]['NameDB'], $guid_doc_metadata, $xml_array['fecha_hora'], $xml_array['emisor']['rfc'], $xml_array['folio'],$logs);
                }*/
            }

            $logs[] = ["tipo" => 0, "descripcion" => "Finaliza"];

            foreach($logs as $log)
            {
                $this->logsADD()->create(
                    [
                        "log_add"=>$log["descripcion"],
                        "tipo"=>$log["tipo"]
                    ]
                );
            }

        }

    }
    private function existDb($guidCompany){
        try{
            $resp = DB::connection('cntpq')->select(DB::raw("exec [DB_Directory].[dbo].[spExistDB] @GuidCompany = '$guidCompany'"));
            $resp_ = json_decode(json_encode($resp), true);
            return $resp_;
        }catch(Exception $e){
            throw new Exception($e->getMessage(),500);
        }
        return false;
    }
    private function insUpdCertificate($llave, $no_serie, $rfc, $r_social){
        $guidDoc = Uuid::generate()->string;
        $issuer_name = 'OID.1.2.840.113549.1.9.2=Responsable: Administración Central de Servicios Tributarios al Contribuyente, OID.2.5.4.45=SAT970701NN3, L=Cuauhtémoc, S=Distrito Federal, C=MX, PostalCode=06300, STREET="Av. Hidalgo 77, Col. Guerrero", E=acods@sat.gob.mx, OU=Administración de Seguridad de la Información, O=Servicio de Administración Tributaria, CN=A.C. del Servicio de Administración Tributaria';
        $subject_name = 'OU=UNICA, SERIALNUMBER=" / ", OID.2.5.4.45='.$rfc.' / , O='.$r_social.', OID.2.5.4.41='.$r_social.', CN='.$r_social;
        try{
            $resp = DB::connection('cntpq')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec [DB_Directory].[dbo].[spInsUpdCertificate] @GuidDocument = 'DD41F3B0-D47A-11EB-82DA-E1114F8D5A0B', @LlavePublica='$llave', @NumeroSerie='$no_serie', @FechaInicial='',
                @FechaFinal='',@SubjectName='$subject_name', @IssuerName='$issuer_name', @IsTesting=0");

            $val = DB::connection('cntpq')->select(DB::raw("SELECT top 1 * FROM [DB_Directory].[dbo].[Certificates] WHERE NumeroSerie='$no_serie'"));
            return $val != false;
        }catch(Exception $e){
            throw new Exception("Error de ejecución del sp spInsUpdCertificate en la base de datos DB_Directory".$e->getMessage().$e->getLine(),500);
        }
        return false;
    }

    private function buscarCfdiDuplicado($base_datos, $uuid){
        try{
            DB::purge('cntpqdm');
            Config::set('database.connections.cntpqdm.database', $base_datos);
            $resp = DB::connection('cntpqdm')->select(DB::raw("SELECT Documento.GuidDocument GuidDocument FROM  Documento WITH(NOLOCK)
        LEFT JOIN Comprobante WITH(NOLOCK) ON Comprobante.GuidDocument = Documento.GuidDocument
        WHERE Comprobante.UUID='$uuid' "));
            return count($resp) > 0;
        }catch(Exception $e){
            throw new Exception("Error de lectura a la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage().$e->getLine(),500);
        }
    }

    private function spInsUpdDocument($xml, $db_doc_metadata, $db_doc_content, $db_other_content, $db_other_metadata, $guid, $doc_date, $rfc, $folio){
        DB::purge('cntpqdm');
        Config::set('database.connections.cntpqdm.database', $db_doc_metadata);
        $hash = md5($guid).'=';

        $micro_date = microtime();
        $date_array = explode(" ",$micro_date);
        $date = date("Y-m-d",$date_array[1]).'T'. date('H:i:s', $date_array[1]) . str_replace('0.', '.', $date_array[0]).'-05:00';

        $xml_data = $this->del_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
        $xml_data = $this->del_string_between($xml, '<?', '?>');

        $pXmlFile = '<Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0"
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="CFDI" xmlns="http://www.contpaqi.com">
        <Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">' . $xml_data.
            '</Document></Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';

        $pXmlFile = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($pXmlFile));
        $pXmlFile = preg_replace('/[ ]{2,}|[\r]/', ' ', trim($pXmlFile));
        $pXmlFile = preg_replace('/[ ]{2,}|[\n]/', ' ', trim($pXmlFile));

        try{
            /*SET NOCOUNT ON; SET ANSI_WARNINGS OFF;
             * */
            $resp = DB::connection('cntpqdm')
                ->update(DB::raw("DECLARE @return_value int  EXEC [$db_doc_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = N'$pXmlFile', @pDeleteDocument=0, @pSobreEscribe=0, @filename=NULL SELECT 'Return Value' = @return_value"));

        }catch(Exception $e){
            throw new Exception("-Error de ejecución de sp spInsUpdDocument en la base de datos: ".Config::get('database.connections.cntpqdm.database')." respuesta: ".$e->getMessage().$e->getLine(),500);
        }

        $val = DB::connection('cntpqdm')->select(DB::raw("SELECT top 1 * FROM [$db_doc_metadata].[dbo].[Comprobante] WHERE [GuidDocument]='$guid'"));
        if(count($val) == 0){
            throw new Exception("respuesta spInsUpdDocument: ".$resp." No se encontro el comprobante con el GUID: ".$guid." en la base de datos: ".Config::get('database.connections.cntpqdm.database'));
        }

        $conceptos = $this->get_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
        $conceptos = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($conceptos));
        $array_concepto = [];
        do{
            $array_concepto[] = $this->get_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
            $conceptos = $this->del_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
        } while(strlen($conceptos) > 50);

        try{
            foreach($array_concepto as $key => $concepto){
                $filename = $guid . '.xml';
                $conceptNumber = $key + 1;
                $pXml_Node = '<cfdi:Concepto xmlns:cfdi="http://www.sat.gob.mx/cfd/3" ' . $concepto . '</cfdi:Concepto>';
                $resp = DB::connection('cntpqdm')
                    ->update("exec [$db_doc_metadata].[dbo].[spInsConcept]  @pGuidDocument=N'$guid',@pXml_Node=N'$pXml_Node', @fileName=N'$filename', @conceptNumber=$conceptNumber");
            }

        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spInsConcept en la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage().$e->getLine(),500);
        }

        $creation_date = date("Y-m-d H:i:s",$date_array[1]);
        try{
            DB::purge('cntpqdc');
            Config::set('database.connections.cntpqdc.database', $db_doc_content);
            $resp = DB::connection('cntpqdc')
                ->update("exec [$db_doc_content].[dbo].[spSaveDocument]  @GuidDocument=N'$guid',@DocumentType=N'CFDI', @fileName=N'$filename' ,@Content=N'$xml'
                            ,@SubDirectory=N'',@DocumentDate=N'$doc_date',@CreationDate=N'$creation_date'");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spSaveDocument en la base de datos: ".Config::get('database.connections.cntpqdc.database').$e->getMessage().$e->getLine(),500);
        }

        try{
            DB::purge('cntpqdc');
            Config::set('database.connections.cntpqdc.database', $db_doc_content);
            $resp = DB::connection('cntpqdc')
                ->select("SELECT top 1 * FROM [$db_doc_content].[dbo].[DocumentContent] where [GuidDocument] = '$guid' ");
            $content = ltrim($resp[0]->Content);
            if(substr($content, 0, 1) == '?'){
                $content = str_replace('?<', '<', $content);
                $upd = DB::connection('cntpqdc')
                    ->update("UPDATE [$db_doc_content].[dbo].[DocumentContent] set Content = '$content' where [GuidDocument] = '$guid' ");
            }
        }catch(Exception $e){
            throw new Exception("Error de ejecución de validación de signo ? al inicio del XML: ".Config::get('database.connections.cntpqdc.database').$e->getMessage().$e->getLine(),500);
        }


        $guid_vr = Uuid::generate()->string;
        $filename_vr = $guid_vr . '.xml';
        $val_result = $this->validationResult($guid_vr, $date, $doc_date, $rfc, $folio);
        try{
            DB::purge('cntpqom');
            Config::set('database.connections.cntpqom.database', $db_other_metadata);
            $resp = DB::connection('cntpqom')
                ->update("exec [$db_other_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = '$val_result', @pDeleteDocument=0, @pSobreEscribe=0");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spInsUpdDocument en la base de datos: ".Config::get('database.connections.cntpqom.database').$e->getMessage(),500);
        }

        try{
            $fecha_sf = date('Y-m-d');
            $resp = DB::connection('cntpqom')
                ->update("exec [$db_other_metadata].[dbo].[spCreateReferences]  @GuidRel =N'$guid' , @RelatedGuidDocuments=N'$guid_vr'
                        ,@ApplicationType=N'ADD',@TipoDoc=N'ValidationResult',@Fecha=N'$fecha_sf',@Comment=N'Acuse Validación Comprobante $doc_date $rfc $folio '");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spCreateReferences en la base de datos: ".Config::get('database.connections.cntpqom.database').$e->getMessage(),500);
        }

        $val_result_corto = $this->get_string_between($val_result, '<ValidationResult', '</ValidationResult>');
        $val_result_corto = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><ValidationResult'. $val_result_corto .'</ValidationResult>';
        try {
            DB::purge('cntpqoc');
            Config::set('database.connections.cntpqoc.database', $db_other_content);
            $resp = DB::connection('cntpqoc')
                ->update("exec [$db_other_content].[dbo].[spSaveDocument]  @GuidDocument=N'$guid_vr',@DocumentType=N'VALIDATIONRESULT', @fileName=N'$filename_vr' ,@Content=N'$val_result_corto'
                            ,@SubDirectory=N'',@DocumentDate=N'$creation_date',@CreationDate=N'$creation_date'");
        }
        catch(Exception $e){
            throw new Exception("Error de ejecución de sp spSaveDocument  en la base de datos: ".Config::get('database.connections.cntpqoc.database').$e->getMessage(),500);
        }
        try {
            $resp = DB::connection('cntpqdm')
                ->update("exec [$db_doc_metadata].[dbo].[spUpdDocumento]  @GuidDocument =N'$guid',@ProcessApp=N'',@UserResponsibleApp=N'',
                            @ReferenceApp=N'',@NotesApp=N'',@MetadataEstatusApp=N'Timbrado',@ValidationStatus=N'OK' ");
        }
        catch(Exception $e){
            throw new Exception("Error de ejecución de sp spUpdDocumento  en la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage(),500);
        }

        return true;

    }

    private function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    private function del_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $len = strpos($string, $end, $ini)-$ini;
        $texto =  substr($string, $ini, $len);
        return str_replace($texto.$end, '', $string);
    }

    private function validationResult($guid, $date, $date_xml, $rfc, $folio){
        $hash = md5($guid).'=';
        return '<?xml version="1.0" encoding="utf-8"?><Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0"
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="ValidationResult"
        xmlns="http://www.contpaqi.com"><Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">
        <ValidationResult documentType="CFDI" IsDuplicated="false" IsValid="true"><RFCIssuer>'.$rfc.'</RFCIssuer><DateIssue>'.$date_xml.'</DateIssue><Serial></Serial><Number>'.$folio.'</Number>
        <ValidationItemResult validationResult="OK" descriptionValidation="Codificación del CFD/CFDI es UTF-8 . " codeValidation="1.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El XML es un comprobante " codeValidation="1.2" /><ValidationItemResult validationResult="OK" descriptionValidation="Estructura  "  codeValidation="1.3" />
        <ValidationItemResult validationResult="OK" descriptionValidation="La versión del comprobante es correcta a su fecha de generación" codeValidation="1.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El número de certificado del comprobante corresponde al certificado reportado " codeValidation="2.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante en base 64 es correcto" codeValidation="2.2" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante fue emitido por el SAT " codeValidation="2.3" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante corresponde a un CSD o FIEL " codeValidation="2.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El sello del comprobante es válido para el certificado reportado " codeValidation="2.8" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante no debe corresponder a un certificado de prueba " codeValidation="2.9" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado corresponde al RFC del Emisor" codeValidation="3.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI Se encontró el complemento Timbre Fiscal Digital " codeValidation="4.3" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI Se encontró el certificado  del PAC   (00001000000504587508)" codeValidation="4.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI El sello del Timbre Fiscal Digital es válido " codeValidation="4.7" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital no debe ser un certificado de prueba " codeValidation="4.8" /><ValidationItemResult
        validationResult="OK" descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital fue emitido para un PAC " codeValidation="4.9" /><ValidationItemResult
        validationResult="OK" descriptionValidation="CFDI El sello CFD del timbre corresponde con el sello del comprobante " codeValidation="4.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="En cargar Recibidos: El RFC del comprobante Recibido corresponde con el RFC de la empresa " codeValidation="5.1" /></ValidationResult></Document>
        </Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';
    }
}
