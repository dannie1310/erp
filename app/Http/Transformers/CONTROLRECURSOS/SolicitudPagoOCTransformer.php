<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolicitudPagoOC;
use League\Fractal\TransformerAbstract;

class SolicitudPagoOCTransformer extends TransformerAbstract
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

    public function transform(SolicitudPagoOC $model){
        return [
            'id' => $model->getKey(),
        ];
    }

}
