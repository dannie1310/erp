<?php


namespace App\Http\Transformers\CTPQ;


use App\Models\CTPQ\Cuenta;
use League\Fractal\TransformerAbstract;

class CuentaTransformer extends TransformerAbstract
{
    public function transform(Cuenta $model) {
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => $model->Codigo,
            'descripcion' => $model->Nombre
        ];
    }
}
