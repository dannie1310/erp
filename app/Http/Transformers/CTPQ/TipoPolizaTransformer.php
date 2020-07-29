<?php


namespace App\Http\Transformers\CTPQ;


use App\Models\CTPQ\TipoPoliza;
use League\Fractal\TransformerAbstract;

class TipoPolizaTransformer extends TransformerAbstract
{
    public function transform(TipoPoliza $model) {
        return [
            'id' => (int) $model->getKey(),
            'tipo' => (string) $model->Nombre
        ];
    }
}
