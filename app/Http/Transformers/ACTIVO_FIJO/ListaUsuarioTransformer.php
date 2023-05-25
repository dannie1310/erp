<?php

namespace App\Http\Transformers\ACTIVO_FIJO;

use App\Models\SCI\VwListaUsuario;
use League\Fractal\TransformerAbstract;

class ListaUsuarioTransformer extends TransformerAbstract
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

    public function transform(VwListaUsuario $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre' => $model->Usuario,
            'ubicacion' => $model->Ubicacion
        ];
    }

}
