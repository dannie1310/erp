<?php


namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\EstatusPrepoliza;
use League\Fractal\TransformerAbstract;

class EstatusPrepolizaTransformer extends TransformerAbstract
{
    public function transform(EstatusPrepoliza $model) {
        return [
            'id' => (int) $model->getKey(),
            'estatus' => (int) $model->estatus,
            'descripcion' => (string) $model->descripcion,
            'color' => (string) $model->label
        ];
    }
}