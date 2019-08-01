<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CtgTipoFondo;
use League\Fractal\TransformerAbstract;

class CtgTipoFondoTransformer extends TransformerAbstract
{
    public function transform(CtgTipoFondo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion_corta'=>$model->descripcion_corta,
            'descripcion' => $model->descripcion
        ];
    }
}