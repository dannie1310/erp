<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CtgTipoCuentaObra;
use League\Fractal\TransformerAbstract;

class CtgTipoCuentaObraTransformer extends TransformerAbstract
{
    public function transform(CtgTipoCuentaObra $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion'=>$model->descripcion,
            'estado' => $model->estatus
        ];
    }
}
