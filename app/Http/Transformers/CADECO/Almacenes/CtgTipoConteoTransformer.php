<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\CtgTipoConteo;
use League\Fractal\TransformerAbstract;

class CtgTipoConteoTransformer extends TransformerAbstract
{
    public function transform(CtgTipoConteo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }

}