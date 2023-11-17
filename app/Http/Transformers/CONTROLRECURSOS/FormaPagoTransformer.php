<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\FormaPago;
use League\Fractal\TransformerAbstract;

class FormaPagoTransformer extends TransformerAbstract
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

    public function transform(FormaPago $model){
        return [
            'id' => $model->getKey(),
            'nombre' => $model->Nombre
        ];
    }
}
