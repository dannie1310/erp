<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use League\Fractal\TransformerAbstract;

class CtgAreaCompradoraTransformer extends TransformerAbstract
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


    public function transform(CtgAreaCompradora $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'descripcion_corta' => $model->descripcion_corta,
        ];
    }
}
