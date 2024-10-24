<?php

namespace App\Http\Transformers\IGH;

use App\Models\IGH\Departamento;
use League\Fractal\TransformerAbstract;

class DepartamentoTransformer extends TransformerAbstract
{
    public function transform(Departamento $model) {

        return [
            'id' => (int) $model->getKey(),
            'departamento' => $model->departamento
        ];
    }
}
