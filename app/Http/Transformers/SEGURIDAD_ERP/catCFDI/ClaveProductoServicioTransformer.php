<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\catCFDI;

use App\Models\SEGURIDAD_ERP\catCFDI\ClaveProductoServicio;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSATConceptos;
use League\Fractal\TransformerAbstract;

class ClaveProductoServicioTransformer extends TransformerAbstract
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

    public function transform(ClaveProductoServicio $model) {
        return [
            'id' => (int) $model->id,
            'clave'=>$model->clave,
            'descripcion'=>$model->descripcion,
        ];
    }
}

