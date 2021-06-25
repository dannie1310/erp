<?php

namespace App\Http\Transformers\CADECO\Subcontrato;

use App\Models\CADECO\Subcontratos\TipoContrato;
use League\Fractal\TransformerAbstract;

class TipoContratoTransformer extends TransformerAbstract
{
    public function transform(TipoContrato $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}