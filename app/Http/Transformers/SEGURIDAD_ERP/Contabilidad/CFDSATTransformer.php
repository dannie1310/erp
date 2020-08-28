<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 12:08 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


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
        'estatus'
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
            'total' => (float) $model->total,
            'total_format' => $model->total_format
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
}
