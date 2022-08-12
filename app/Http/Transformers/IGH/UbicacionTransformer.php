<?php


namespace App\Http\Transformers\IGH;


use League\Fractal\TransformerAbstract;

class UbicacionTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model->idubicacion,
            'ubicacion'=>$model->ubicacion,
            'abreviatura'=>$model->ubicacion_abreviatura,
        ];
    }
}
