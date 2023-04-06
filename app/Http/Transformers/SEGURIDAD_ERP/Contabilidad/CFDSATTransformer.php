<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 12:08 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Http\Transformers\CADECO\Finanzas\FacturaTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Documentacion\ArchivoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccionTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\FacturaRepositorioTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\CtgEstadosCFDTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use League\Fractal\TransformerAbstract;

class CFDSATTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'estatus',
        'conceptos',
        'empresa',
        'proveedor',
        'archivos',
        'tipo_transaccion'
    ];

    protected $availableIncludes = [
        'empresa',
        'proveedor',
        'estatus',
        'factura_repositorio',
        'poliza_cfdi',
        'conceptos',
        'cfdi_asociado',
        'transaccion_factura',
        'archivos',
        'tipo_transaccion'
    ];

    public function transform(CFDSAT $model) {
        return [
            'id' => (int) $model->id,
            'serie'=>$model->serie,
            'folio'=>$model->folio,
            'referencia'=>$model->serie." ".$model->folio,
            'fecha'=>$model->fecha,
            'fecha_format' => $model->fecha_format,
            'rfc_emisor' => $model->rfc_emisor,
            'rfc_receptor' => $model->rfc_receptor,
            'iva' => $model->importe_iva,
            'porcentaje_iva' => $model->porcentaje_iva,
            'uuid' => $model->uuid,
            'subtotal' => (float) $model->subtotal,
            'subtotal_format' => $model->subtotal_format,
            'descuento' => (float) $model->descuento,
            'descuento_format' => $model->descuento_format,
            'impuestos_trasladados' => (float) $model->total_impuestos_trasladados,
            'impuestos_trasladados_format' => $model->total_impuestos_trasladados_format,
            'impuestos_retenidos' => (float) $model->total_impuestos_retenidos,
            'impuestos_retenidos_format' => $model->total_impuestos_retenidos_format,
            'total' => (float) $model->total,
            'total_format' => $model->total_format,
            'tipo_comprobante' => $model->tipo_comprobante,
            'moneda' => $model->moneda,
            'tipo_cambio' => $model->tipo_cambio,
            'tipo_relacion' => $model->tipo_relacion,
            'estado' => $model->cancelado == 1? 'Cancelado':'Vigente',
            'estado_lbl' => $model->cancelado == 1? 'Cancelado':'Vigente',
            'estado_color' => $model->estado_color,
            'total_xls' => (float) $model->total_xls,
        ];
    }

    /**
     * @param CFDSAT $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(CFDSAT $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaSATTransformer);
        }
        return null;
    }

    /**
     * @param CFDSAT $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(CFDSAT $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorSATTransformer);
        }
        return null;
    }

    /**
     * @param CFDSAT $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEstatus(CFDSAT $model)
    {
        if($estatus = $model->ctgEstado)
        {
            return $this->item($estatus, new CtgEstadosCFDTransformer);
        }
        return null;
    }

    public function includeFacturaRepositorio(CFDSAT $model)
    {
        if($item = $model->facturaRepositorio)
        {
            return $this->item($item, new FacturaRepositorioTransformer);
        }
        return null;
    }

    public function includePolizaCFDI(CFDSAT $model)
    {
        if($item = $model->polizaCFDI)
        {
            return $this->item($item, new PolizaCFDITransformer);
        }
        return null;
    }

    public function includeConceptos(CFDSAT $model)
    {
        if($items = $model->conceptos)
        {
            return $this->collection($items, new CFDSATConceptosTransformer);
        }
        return null;
    }

    public function includeArchivos(CFDSAT $model)
    {
        if($items = $model->archivos()->orderBy("obligatorio", "desc")->get())
        {
            return $this->collection($items, new ArchivoTransformer);
        }
        return null;
    }

    public function includeCFDIAsociado(CFDSAT $model)
    {
        if($item = $model->asociado)
        {
            return $this->item($item, new CFDSATTransformer);
        }
        return null;
    }

    public function includeTransaccionFactura(CFDSAT $model)
    {
        try{
            if($item = $model->facturaRepositorio->transaccion_factura)
            {
                return $this->item($item, new FacturaTransformer);
            }
            return null;
        }catch (\Exception $e)
        {
            return null;
        }

    }

    public function includeTipoTransaccion(CFDSAT $model)
    {
        try{
            if($item = $model->tipoTransaccion)
            {
                return $this->item($item, new CtgTipoTransaccionTransformer);
            }
            return null;
        }catch (\Exception $e)
        {
            return null;
        }

    }
}
