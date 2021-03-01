<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSATConceptos;
use League\Fractal\TransformerAbstract;

class CFDSATConceptosTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     * @var array
     */
    protected $defaultIncludes = [];
    /** List of resources to available include
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(CFDSATConceptos $model) {
        return [
            'id' => (int) $model->id,
            'cantidad'=>$model->cantidad,
            'clave_prod_serv'=>$model->clave_prod_serv,
            'clave_unidad'=>$model->clave_unidad,
            'descripcion'=>$model->descripcion,
            'descuento'=>$model->descuento,
            'importe'=>$model->importe,
            'unidad'=>$model->unidad,
            'valor_unitario'=>$model->valor_unitario,
        ];
    }
}

