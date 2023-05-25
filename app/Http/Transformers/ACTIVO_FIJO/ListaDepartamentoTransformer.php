<?php

namespace App\Http\Transformers\ACTIVO_FIJO;

use App\Models\SCI\VwListaDepartamento;
use League\Fractal\TransformerAbstract;

class ListaDepartamentoTransformer extends TransformerAbstract
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

    public function transform(VwListaDepartamento $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre' => $model->Departamento
        ];
    }
}
