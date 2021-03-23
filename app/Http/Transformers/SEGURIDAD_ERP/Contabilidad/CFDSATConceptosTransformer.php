<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Http\Transformers\SEGURIDAD_ERP\catCFDI\ClaveProductoServicioTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSATConceptos;
use League\Fractal\TransformerAbstract;

class CFDSATConceptosTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     * @var array
     */
    protected $defaultIncludes = ['clave_producto_servicio','traslados'];
    /** List of resources to available include
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(CFDSATConceptos $model) {
        return [
            'id' => (int) $model->id,
            'cantidad'=>$model->cantidad,
            'cantidad_format'=>$model->cantidad_format,
            'clave_prod_serv'=>$model->clave_prod_serv,
            'clave_unidad'=>$model->clave_unidad,
            'descripcion'=>$model->descripcion_sat." | ".$model->descripcion,
            'descripcion_sat'=>$model->descripcion_sat,
            'descuento'=>$model->descuento,
            'descuento_format'=>$model->descuento_format,
            'importe'=>$model->importe,
            'importe_format'=>$model->importe_format,
            'unidad'=>$model->unidad,
            'valor_unitario'=>$model->valor_unitario,
            'valor_unitario_format'=>$model->valor_unitario_format,
        ];
    }

    public function includeClaveProductoServicio(CFDSATConceptos $model)
    {
        if($item = $model->claveProductoServicio)
        {
            return $this->item($item, new ClaveProductoServicioTransformer);
        }
        return null;
    }

    public function includeTraslados(CFDSATConceptos $model)
    {
        if($items = $model->traslados)
        {
            return $this->collection($items, new CFDSATTrasladoTransformer);
        }
        return null;
    }
}

