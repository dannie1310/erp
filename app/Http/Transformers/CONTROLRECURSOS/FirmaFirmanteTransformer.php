<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\FirmaFirmante;
use League\Fractal\TransformerAbstract;

class FirmaFirmanteTransformer extends TransformerAbstract
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

    public function transform(FirmaFirmante $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'descripcion_st' => $model->descripcion_st
        ];
    }
}
