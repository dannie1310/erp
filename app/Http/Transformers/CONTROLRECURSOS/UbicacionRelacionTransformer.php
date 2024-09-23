<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\VwUbicacionRelacion;
use League\Fractal\TransformerAbstract;

class UbicacionRelacionTransformer extends TransformerAbstract
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

    public function transform(VwUbicacionRelacion $model){
        return [
            'id' => $model->getKey(),
            'ubicacion' => $model->ubicacion
        ];
    }

}
