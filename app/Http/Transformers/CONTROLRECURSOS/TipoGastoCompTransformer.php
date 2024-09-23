<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TipoGastoComp;
use League\Fractal\TransformerAbstract;

class TipoGastoCompTransformer extends TransformerAbstract
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

    public function transform(TipoGastoComp $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->Descripcion
        ];
    }
}
