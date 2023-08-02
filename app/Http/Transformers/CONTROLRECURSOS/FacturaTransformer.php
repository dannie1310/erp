<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Factura;
use League\Fractal\TransformerAbstract;

class FacturaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Factura $model){
        return [
            'id' => $model->getKey(),
            'folio_format' => $model->folio_con_serie,
            'concepto' => $model->Concepto,
            'fecha' => $model->Fecha,
            'total_format' => $model->total_format,
            'moneda' => $model->moneda_descripcion,
            'serie' => $model->serie_descripcion,
            'tipo_documento' => $model->tipo_documento
        ];
    }
}
