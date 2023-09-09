<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\RelacionGasto;

class RelacionGastoTransformer
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

    public function transform(RelacionGasto $model){
        return [
            'id' => $model->getKey(),
        ];
    }
}
