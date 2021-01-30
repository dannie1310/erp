<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 12:08 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


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
        'estatus'
    ];

    protected $availableIncludes = [
        'empresa',
        'proveedor',
        'estatus',
        'factura_repositorio'
    ];

    public function transform(CFDSAT $model) {
        return [
            'id' => (int) $model->id,
            'serie'=>$model->serie,
            'folio'=>$model->folio,
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
            'estado' => $model->estado_txt
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
}
