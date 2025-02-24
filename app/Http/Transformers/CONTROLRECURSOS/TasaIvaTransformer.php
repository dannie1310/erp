<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TasaIva;
use League\Fractal\TransformerAbstract;

class TasaIvaTransformer extends TransformerAbstract
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

    public function transform(TasaIva $model){
        return [
            'id' => $model->getKey(),
            'tasa_iva' => $model->tasa_iva
        ];
    }
}
