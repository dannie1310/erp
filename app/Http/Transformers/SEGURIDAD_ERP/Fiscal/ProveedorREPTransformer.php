<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use League\Fractal\TransformerAbstract;

class ProveedorREPTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'poliza'
    ];

    public function transform(ProveedorREP $model)
    {
        return [
            'id' => $model->getKey(),
            'rfc_proveedor' => $model->rfc_proveedor,
            'proveedor' => mb_strtoupper($model->proveedor),
            'cantidad_cfdi' => $model->cantidad_cfdi_format,
            'total_cfdi' => $model->total_cfdi_format,
            'total_rep' => $model->total_rep_format,
            'pendiente_rep' => $model->pendiente_rep_format,
            'es_empresa_hermes' => $model->es_empresa_hermes,
        ];
    }

}
