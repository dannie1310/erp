<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Http\Transformers\SEGURIDAD_ERP\catCFDI\ClaveProductoServicioTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSATConceptos;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSATTraslados;
use League\Fractal\TransformerAbstract;

class CFDSATTrasladoTransformer extends TransformerAbstract
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

    public function transform(CFDSATTraslados $model) {
        return [
            'id' => (int) $model->id,
            'tipo_factor'=>$model->tipo_factor,
            'tasa_o_cuota'=>$model->tasa_o_cuota,
            'importe'=>$model->importe,
            'impuesto'=>$model->impuesto,
            'impuesto_txt'=>$model->impuesto_txt,
            'base'=>$model->base,
            'base_format'=>$model->base_format,
            'importe_format'=>$model->importe_format,

        ];
    }

}

