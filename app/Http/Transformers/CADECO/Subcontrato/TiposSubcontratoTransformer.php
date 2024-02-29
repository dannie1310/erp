<?php

namespace App\Http\Transformers\CADECO\Subcontrato;

use App\Models\CADECO\Subcontratos\TiposSubcontrato;
use League\Fractal\TransformerAbstract;

class TiposSubcontratoTransformer extends TransformerAbstract
{
    public function transform(TiposSubcontrato $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}