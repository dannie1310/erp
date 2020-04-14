<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use League\Fractal\TransformerAbstract;

class TipoAreaCompradoraTransformer extends TransformerAbstract
{

    public function transform(CtgAreaCompradora $model){

        return[
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'descripcion_corta' => $model->descripcion_corta,
        ];
    }
}
