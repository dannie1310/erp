<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Operador;
use League\Fractal\TransformerAbstract;

class OperadorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(Operador $model) {
        return [
            'id' => (int) $model->getKey(),
            'nombre' => (string) $model->Nombre
        ];
    }
}
