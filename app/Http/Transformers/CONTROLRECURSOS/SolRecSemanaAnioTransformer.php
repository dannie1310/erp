<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolrecSemanaAnio;
use League\Fractal\TransformerAbstract;

class SolRecSemanaAnioTransformer extends TransformerAbstract
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

    public function transform(SolrecSemanaAnio $model){
        return [
            'id' => $model->getKey(),
            'semana' => $model->semana,
            'anio' => $model->anio,
        ];
    }
}
