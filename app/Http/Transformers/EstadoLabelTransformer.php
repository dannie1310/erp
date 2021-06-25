<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class EstadoLabelTransformer extends TransformerAbstract
{

    public function transform($model) {
        return [
            'color' => $model["color"],
            'descripcion' => (string) $model["descripcion"],
        ];
    }

}
