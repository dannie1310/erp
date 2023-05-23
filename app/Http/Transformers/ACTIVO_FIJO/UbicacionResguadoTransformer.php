<?php

namespace App\Http\Transformers\ACTIVO_FIJO;

use App\Models\SCI\VwUbicacionResguado;
use League\Fractal\TransformerAbstract;

class UbicacionResguadoTransformer extends TransformerAbstract
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

    public function transform(VwUbicacionResguado $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre' => $model->Ubicacion
        ];
    }

}
