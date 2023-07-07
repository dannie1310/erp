<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\PolizaMovimiento;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa as EmpresaERP;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class LayoutPasivoPartida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.layout_pasivos_partidas';
    protected $fillable =[
        "id_carga",
        "obra",
        "bbdd_contpaq",
        "rfc_empresa",
        "empresa",
        "rfc_proveedor",
        "proveedor",
        "concepto",
        "folio_factura",
        "fecha_factura",
        "importe_factura",
        "moneda_factura",
        "tc_factura",
        "importe_mxn",
        "saldo",
        "uuid",
        "coincide_fecha",
        "coincide_folio",
        "coincide_importe",
        "coincide_moneda",
        "coincide_rfc_empresa",
        "coincide_rfc_proveedor",
        "coincide_tipo_cambio",

    ];
    public $timestamps = false;

    /**
     * Relaciones
     */

    public function layout()
    {
        return $this->belongsTo(LayoutPasivoCarga::class,"id","id_carga");
    }

    public function CFDI()
    {
        return $this->hasOne(CFDSAT::class,"uuid","uuid");
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */

    public function getImporteFacturaFormatAttribute($key)
    {
        return number_format($this->importe_factura,2);
    }

    public function getImporteMxnFormatAttribute($key)
    {
        return number_format($this->importe_mxn,2);
    }

    public function getTcFacturaFormatAttribute($key)
    {
        return number_format($this->tc_factura,4);
    }

    public function getSaldoMxnAttribute()
    {
        if($this->CFDI)
        {
            if($this->CFDI->moneda != "MXN"){
                if($this->CFDI->tipo_cambio>0){
                    return $this->saldo * $this->CFDI->tipo_cambio;
                }else{
                    return $this->saldo;
                }
            }else{
                return $this->saldo;
            }
        }
    }

    public function getSaldoFormatAttribute($key)
    {
        return number_format($this->saldo,2);
    }

    public function getFechaFacturaFormatAttribute()
    {
        $date = date_create($this->fecha_factura);
        return date_format($date, "d/m/Y");
    }

    public function getPosiblesCFDIAttribute()
    {
        $empresa_erp = EmpresaERP::where("AliasBDD","=",$this->bbdd_contpaq)
            ->first();
        $id_empresa_contpaq = $empresa_erp->IdEmpresaContpaq;


        if(!$empresa_erp->empresaSAT)
        {
            throw new \Exception("No hay una empresa SAT asociada a la empresa de Contabilidad",500);
        }



        $uuid_cfdi_asociados = $this->CFDI ? $this->CFDI->pluck("uuid")
            ->toArray():[];


        $uuid_cfdi_asociados = array_map('strtoupper', $uuid_cfdi_asociados);

        $id_proveedor_sat = ProveedorSAT::where("rfc", "=",$this->rfc_proveedor)
            ->get()
            ->pluck("id")
            ->toArray();


        $importe = $this->importe_factura;

        $referencia = $this->folio_factura;


        $query = CFDSAT::
        join("Contabilidad.proveedores_sat","proveedores_sat.id","cfd_sat.id_proveedor_sat")
            ->where("cancelado","=",0)
            ->where("tipo_comprobante","=","I")
            ->where("id_empresa_sat","=",$empresa_erp->IdEmpresaSAT);

        if($id_proveedor_sat>0)
        {
            $query->whereIn("id_proveedor_sat",$id_proveedor_sat);
            $query->Where("total","=",$importe)
            ;
        }else{
            $query->orWhere("total","=",$importe);
        }
        $query->selectRaw("cfd_sat.id_proveedor_sat, cfd_sat.id, cfd_sat.uuid, cfd_sat.importe_iva, cfd_sat.total,cfd_sat.conceptos_txt
            ,cfd_sat.serie, cfd_sat.folio, cfd_sat.fecha, cfd_sat.moneda, proveedores_sat.rfc, proveedores_sat.razon_social
            , FORMAT(cfd_sat.fecha,'dd/MM/yyyy') as fecha_cfdi, 1 as grado_coincidencia, 0 as seleccionado, cfd_sat.tipo_comprobante")
            ->orderBy("cfd_sat.total")
        ;

        $cfdis = $query->get();

        $nuevos_cfdi = $cfdis->filter(function ($item) use($uuid_cfdi_asociados){
            if(!in_array(strtoupper($item->uuid),$uuid_cfdi_asociados)){
                return $item;
            }
        });

        $nuevos_cfdi = $nuevos_cfdi->map(
            function ($cfdi) use ($importe, $id_proveedor_sat, $referencia)
            {
                $cfdi->seleccionado = false;
                $cfdi->coincide_rfc_empresa = 1;
                $cfdi->coincide_importe = 0;
                $cfdi->coincide_rfc_proveedor = 0;
                $cfdi->coincide_fecha = 0;
                $cfdi->coincide_folio = 0;
                $cfdi->coincide_moneda = 0;

                if(abs($cfdi->total- $importe)<1)
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_importe = 1;
                }
                if(in_array($cfdi->id_proveedor_sat, $id_proveedor_sat))
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_rfc_proveedor = 1;
                }
                if($cfdi->fecha_cfdi == $this->fecha_factura_format)
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_fecha = 1;
                }
                if(strpos($referencia,$cfdi->folio)!==false)
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_folio = 1;
                }
                if($cfdi->moneda == $this->moneda_factura)
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_moneda = 1;
                }
                return $cfdi;
            }

        )
            ->sortByDesc("grado_coincidencia")
        ;


        return $nuevos_cfdi;
    }


    /**
     * Métodos
     */
}
