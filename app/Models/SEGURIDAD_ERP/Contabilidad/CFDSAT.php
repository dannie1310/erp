<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:27 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\catCFDI\TipoComprobante;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgEstadoCFD;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Utils\CFD;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        ,"fecha_pago"
        ,"id_tipo_transaccion"
        ,"conceptos_txt"
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

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y H:i:s");
    }

    public static function getFechaUltimoCFDTxt()
    {
        $ultimo_cfd = CFDSAT::orderBy("fecha","desc")->first();
        $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
        $mes = $meses[($ultimo_cfd->fecha->format('n')) - 1];
        $fecha = "CFDI cargados al ".$ultimo_cfd->fecha->format("d")." de ".$mes. " de ".$ultimo_cfd->fecha->format("Y");
        return $fecha;
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->total),2);
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
}
