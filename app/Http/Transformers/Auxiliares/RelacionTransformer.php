<?php


namespace App\Http\Transformers\Auxiliares;


use League\Fractal\TransformerAbstract;

class RelacionTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model["id"],
            'tipo' => $model["tipo"],
        ];
    }

}
