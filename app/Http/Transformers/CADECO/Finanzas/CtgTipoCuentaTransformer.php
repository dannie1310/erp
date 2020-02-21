<?php


namespace App\Http\Transformers\CADECO\Finanzas;

use App\Models\CADECO\FinanzasCBE\CtgTipoCuenta;
use League\Fractal\TransformerAbstract;

class CtgTipoCuentaTransformer extends TransformerAbstract
{

    public function transform(CtgTipoCuenta $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}