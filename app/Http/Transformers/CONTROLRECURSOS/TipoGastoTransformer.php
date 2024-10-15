<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TipoGasto;
use League\Fractal\TransformerAbstract;

class TipoGastoTransformer extends TransformerAbstract
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

    public function transform(TipoGasto $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => utf8_decode($model->Descripcion)
        ];
    }
}
