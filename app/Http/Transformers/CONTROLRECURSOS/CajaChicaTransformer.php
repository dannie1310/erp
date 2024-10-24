<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CajaChica;
use League\Fractal\TransformerAbstract;

class CajaChicaTransformer extends TransformerAbstract
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

    public function transform(CajaChica $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion_caja
        ];
    }
}
