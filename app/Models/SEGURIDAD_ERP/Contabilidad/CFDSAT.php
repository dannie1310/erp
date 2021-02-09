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
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgEstadoCFD;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Proyecto;
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
    ];

    protected $dates =["fecha", "fecha_cancelacion"];
    //protected $dateFormat = 'Y-m-d H:i:s';

    public function carga()
    {
        return $this->belongsTo(CargaCFDSAT::class, 'id_carga_cfd_sat', 'id');
    }

    public function conceptos()
    {
        return $this->hasMany(CFDSATConceptos::class, 'id_cfd_sat', 'id');
    }

    public function traslados()
    {
        return $this->hasMany(CFDSATTraslados::class, 'id_cfd_sat', 'id');
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

    public function autocorreccion()
    {
        return $this->hasOne(CFDAutocorreccion::class, "id_cfd_sat", "id");
    }

    public function ctgEstado()
    {
        return $this->belongsTo(CtgEstadoCFD::class, 'estado', 'id');
    }

    public function facturaRepositorio()
    {
        return $this->hasOne(FacturaRepositorio::class, "id", "id_factura_repositorio");
    }

    public function polizaCFDI()
    {
        return $this->hasOne(PolizaCFDI::class, "id_cfdi", "id");
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
            ->join(Context::getDatabase().".dbo.transacciones","empresas.id_empresa","=","transacciones.id_empresa")
            ->leftJoin("Finanzas.repositorio_facturas","repositorio_facturas.uuid","=","cfd_sat.uuid")
            ->where("transacciones.id_obra","=",Context::getIdObra())
            ->whereNull("repositorio_facturas.uuid")
            ->orWhere("repositorio_facturas.id_proyecto","=", $proyecto_contexto)->where("repositorio_facturas.id_obra","=",Context::getIdObra())
            ->select("cfd_sat.*")->distinct()
           ;
    }

    public function registrar($data)
    {
        $factura = null;
        try {
            DB::connection('seguridad')->beginTransaction();

            $cfd = $this->create($data);
            if(key_exists("conceptos",$data)){
                foreach($data["conceptos"] as $concepto){
                    $cfd->conceptos()->create($concepto);
                }
            }

            if(key_exists("traslados",$data)){
                foreach($data["traslados"] as $traslado){
                    $cfd->traslados()->create($traslado);
                }
            }
            DB::connection('seguridad')->commit();
            return $cfd;

        } catch (\Exception $e) {
            dd($data);
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public static function getFechaUltimoCFDTxt()
    {
        $ultimo_cfd = CFDSAT::orderBy("fecha","desc")->first();
        $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
        $mes = $meses[($ultimo_cfd->fecha->format('n')) - 1];
        $fecha = "CFD cargados al ".$ultimo_cfd->fecha->format("d")." de ".$mes. " de ".$ultimo_cfd->fecha->format("Y");
        return $fecha;
    }

    public function scopePorProveedor($query, $id_proveedor)
    {
        return $query->where('id_proveedor_sat', '=', $id_proveedor);
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

    public function getTotalFormatAttribute()
    {
        return '$ ' . number_format(($this->total),2);
    }

    public function getSubtotalFormatAttribute()
    {
        return '$ ' . number_format(($this->subtotal),2);
    }

    public function getDescuentoFormatAttribute()
    {
        return '$ ' . number_format(($this->descuento),2);
    }

    public function getTotalImpuestosRetenidosFormatAttribute()
    {
        return '$ ' . number_format(($this->total_impuestos_retenidos),2);
    }

    public function getTotalImpuestosTrasladadosFormatAttribute()
    {
        return '$ ' . number_format(($this->total_impuestos_trasladados),2);
    }

    public function getXMLAttribute()
    {
        $xml = DB::table("Contabilidad.cfd_sat")
            ->select(DB::raw("'data:text/xml;base64,' + CONVERT(varchar(MAX), xml_file ,0) as xml"))
            ->where("id",$this->id)
            ->first();
        return $xml->xml;
    }
}
