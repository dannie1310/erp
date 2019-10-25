<?php


namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\TipoAreaCompradora;
use League\Fractal\TransformerAbstract;

class TipoAreaCompradoraTransformer extends TransformerAbstract
{

    public function transform(TipoAreaCompradora $model){

        return[
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
        ];

    }

}
