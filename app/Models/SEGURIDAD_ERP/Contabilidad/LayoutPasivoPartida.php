<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\PolizaMovimiento;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa as EmpresaERP;
use App\Utils\CFD;
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
        "tc_saldo",
        "saldo_mxn",
        "uuid",
        "coincide_fecha",
        "coincide_folio",
        "coincide_importe",
        "coincide_moneda",
        "coincide_rfc_empresa",
        "coincide_rfc_proveedor",
        "coincide_tipo_cambio",
        "inconsistencia_saldo",
        "es_moneda_nacional",
        "id_caso_sin_cfdi"
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

    public function casoSinCFDI()
    {
        return $this->belongsTo(LayoutPasivoCasoSinCFDI::class,"id_caso_sin_cfdi", "id");
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

    public function getTcSaldoFormatAttribute($key)
    {
        return number_format($this->tc_saldo,4);
    }

    public function getSaldoMxnFormatAttribute($key)
    {
        return number_format($this->saldo_mxn,2);
    }

    public function getSaldoCalculadoMxnAttribute()
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

        $id_empresa_sat = $empresa_erp->IdEmpresaSAT;

        $uuid_cfdi_asociados = [];

        $id_proveedor_sat = ProveedorSAT::where("rfc", "=",$this->rfc_proveedor)
            ->pluck("id")
            ->first();

        $importe = $this->importe_factura;

        $referencia = $this->folio_factura;

        $query = CFDSAT::
        join("Contabilidad.proveedores_sat","proveedores_sat.id","cfd_sat.id_proveedor_sat")
            ->where("tipo_comprobante","=","I")
            ->where("id_empresa_sat","=",$id_empresa_sat);

        if($id_proveedor_sat>0)
        {
            $query->where("id_proveedor_sat",$id_proveedor_sat);
        }
        $query->WhereBetween("total",[$importe-1, $importe+1]);
        $query->selectRaw("cfd_sat.id_proveedor_sat, cfd_sat.id_empresa_sat, cfd_sat.id, cfd_sat.uuid, cfd_sat.importe_iva, cfd_sat.total,cfd_sat.conceptos_txt
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
            function ($cfdi) use ($importe, $id_proveedor_sat, $referencia, $id_empresa_sat)
            {
                $cfdi->seleccionado = false;
                $cfdi->coincide_rfc_empresa = 0;
                $cfdi->coincide_importe = 0;
                $cfdi->coincide_rfc_proveedor = 0;
                $cfdi->coincide_fecha = 0;
                $cfdi->coincide_folio = 0;
                $cfdi->coincide_moneda = 0;

                if(abs($cfdi->total- $importe)<=1)
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_importe = 1;
                }
                if($cfdi->id_empresa_sat == $id_empresa_sat)
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_rfc_empresa = 1;
                }
                if($cfdi->id_proveedor_sat == $id_proveedor_sat)
                {
                    $cfdi->grado_coincidencia += 1;
                    $cfdi->coincide_rfc_proveedor = 1;
                }
                if($cfdi->fecha_cfdi == $this->fecha_factura_format)
                {
                    $cfdi->grado_coincidencia += 3;
                    $cfdi->coincide_fecha = 1;
                }else{
                    $fecha_cfdi_exp = explode("/",$cfdi->fecha_cfdi );
                    $fecha_facrura_exp = explode("/", $this->fecha_factura_format );

                    if($fecha_cfdi_exp[0] == $fecha_facrura_exp[0])
                    {
                        $cfdi->grado_coincidencia += 1;
                    }
                    if($fecha_cfdi_exp[1] == $fecha_facrura_exp[1])
                    {
                        $cfdi->grado_coincidencia += 1;
                    }
                    if($fecha_cfdi_exp[2] == $fecha_facrura_exp[2])
                    {
                        $cfdi->grado_coincidencia += 1;
                    }
                }
                if($cfdi->folio != "" && $referencia != "" && (strpos($referencia, $cfdi->folio)!==false
                        || strpos($cfdi->folio, $referencia)!==false ))
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
            ->sortBy("grado_coincidencia")
        ;


        return $nuevos_cfdi;
    }

    public function getCasoSinCFDITxtAttribute()
    {
        if($this->casoSinCFDI)
        {
            return $this->casoSinCFDI->descripcion;
        }
        return null;
    }


    /**
     * Métodos
     */

    public function actualizaCoincidenciasConCFDI()
    {
        if($this->CFDI)
        {
            $id_proveedor_sat = ProveedorSAT::where("rfc", "=",$this->rfc_proveedor)
                ->pluck("id")
                ->first();

            $cfdi = CFDSAT::where("id","=",$this->CFDI->id)
                ->selectRaw("cfd_sat.id_proveedor_sat, cfd_sat.id, cfd_sat.uuid, cfd_sat.importe_iva, cfd_sat.total,cfd_sat.conceptos_txt
            ,cfd_sat.serie, cfd_sat.folio, cfd_sat.fecha, cfd_sat.moneda
            , FORMAT(cfd_sat.fecha,'dd/MM/yyyy') as fecha_cfdi, 1 as grado_coincidencia, 0 as seleccionado, cfd_sat.tipo_comprobante")
                ->first()
            ;

            if(abs($cfdi->total- $this->importe_factura)<1)
            {
                $this->coincide_importe = 1;
            }
            if($cfdi->id_proveedor_sat == $id_proveedor_sat)
            {
                $this->coincide_rfc_proveedor = 1;
            }
            if($cfdi->fecha_cfdi == $this->fecha_factura_format)
            {
                $this->coincide_fecha = 1;
            }

            if($cfdi->folio != "" && (strpos($this->folio_factura, $cfdi->folio)!==false
               || strpos($cfdi->folio, $this->folio_factura)!==false ))
            {
                $this->coincide_folio = 1;
            }
            if($cfdi->moneda == $this->moneda_factura)
            {
                $this->coincide_moneda = 1;
            }

            $this->save();
        }

    }

    public function actualizaInconsistenciaSaldo()
    {
        $this->inconsistencia_saldo = (($this->importe_factura * $this->tc_saldo) - $this->saldo_mxn) < -1 ? 1 :0;
        $this->save();

    }

    public function asociaCFDI()
    {
        $monedas_nacionales = LayoutPasivoMonedaNacional::all()->pluck("descripcion")->toArray();

        $posibles = $this->posibles_cfdi;
        if(count($posibles)>0)
        {
            $mejor_coincidencia = $posibles->last();

            $this->id_caso_sin_cfdi = null;
            $this->uuid = $mejor_coincidencia->uuid;
            $this->coincide_rfc_empresa = $mejor_coincidencia->coincide_rfc_empresa;
            $this->coincide_rfc_proveedor = $mejor_coincidencia->coincide_rfc_proveedor;
            $this->coincide_folio = $mejor_coincidencia->coincide_folio;
            $this->coincide_fecha = $mejor_coincidencia->coincide_fecha;
            $this->coincide_importe = $mejor_coincidencia->coincide_importe;
            $this->coincide_moneda = $mejor_coincidencia->coincide_moneda;

            if(!$mejor_coincidencia->moneda)
            {
                $cfd_util = new CFD($mejor_coincidencia->xml);
                $arreglo_cfd = $cfd_util->getArregloFactura();
                $cfd_sat = CFDSAT::find($mejor_coincidencia->id);

                try {
                    if(key_exists("moneda",$arreglo_cfd)){
                        $cfd_sat->moneda = $arreglo_cfd["moneda"];
                        $cfd_sat->save();
                        $mejor_coincidencia->moneda = $arreglo_cfd["moneda"];
                    }
                }
                catch (\Exception $e)
                {
                }
            }

            if(in_array($mejor_coincidencia->moneda, $monedas_nacionales)){

                if(in_array($this->moneda_factura, $monedas_nacionales))
                {
                    $this->es_moneda_nacional = 1;
                    $this->coincide_moneda = 1;
                    $this->tc_factura = 1;
                    $this->tc_saldo = 1;
                    $this->moneda_factura = $mejor_coincidencia->moneda;
                }
            }else{
                $this->es_moneda_nacional = 0;
            }

            if($this->coincide_rfc_empresa &&
                $this->coincide_rfc_proveedor &&
                $this->coincide_folio &&
                $this->coincide_importe &&
                $this->coincide_moneda &&
                !$this->coincide_fecha
            )
            {
                $this->coincide_fecha = true;
                $this->fecha_factura = $mejor_coincidencia->fecha;
            }

            $this->save();
            $this->actualizaInconsistenciaSaldo();
        }
        return $this;
    }
}
