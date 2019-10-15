<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CtgEstadoLayoutPago;
use League\Fractal\TransformerAbstract;


class CtgEstadoLayoutPagoTransformer extends TransformerAbstract
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

    public function transform(CtgEstadoLayoutPago $model){


        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'estado' =>$model->estado,
        ];
    }
}
