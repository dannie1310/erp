<?php


namespace App\Http\Transformers\CADECO\Configuracion;


use App\Models\CADECO\Configuracion\CtgTipoNodo;
use League\Fractal\TransformerAbstract;

class CtgTipoNodoTransformer extends TransformerAbstract
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

    public function transform(CtgTipoNodo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
        ];
    }

}
