<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolCheque;
use League\Fractal\TransformerAbstract;

class SolicitudChequeTransformer extends TransformerAbstract
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

    public function transform(SolCheque $model){
        return [
            'id' => $model->getKey(),
            'concepto' => $model->concepto
        ];
    }
}
