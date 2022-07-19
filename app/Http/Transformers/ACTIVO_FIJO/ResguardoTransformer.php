<?php


namespace App\Http\Transformers\ACTIVO_FIJO;


use League\Fractal\TransformerAbstract;

class ResguardoTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model->idResguardo,
        ];
    }
}
