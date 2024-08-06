<?php

namespace App\Http\Transformers\CTPQ_NOM;

use App\Models\CTPQ\NmNominas\Nom10015;
use League\Fractal\TransformerAbstract;

class PolizaTransformer extends TransformerAbstract
{
    public function transform(Nom10015 $model) {
        return [
            'id' => (int) $model->getKey(),
            'estado' => (string) $model->estadocontab,
            'numeropoliza' => (string) $model->numeropoliza,
            'concepto' => (string) $model->concepto,
            'fecha' => (string) $model->fechapoliza,
            'fecha_format' => $model->fecha_format,
            'ejercicio' => $model->ejercicio
        ];
    }
}
