<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\Marbete;
use League\Fractal\TransformerAbstract;

class MarbeteTransformer extends TransformerAbstract
{
    public function transform(Marbete $model)
    {
        return [
            'id' => $model->getKey(),
            'folio' => $model->folio
        ];
    }

}