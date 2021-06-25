<?php

namespace App\Http\Transformers\MODULOSSAO\Proyectos;

use App\Models\MODULOSSAO\Proyectos\TipoProyecto;
use League\Fractal\TransformerAbstract;

class TipoProyectoTransformer extends TransformerAbstract
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

    public function transform(TipoProyecto $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->TipoProyecto,
        ];
    }
}
