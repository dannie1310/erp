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
            'ejercicio' => $model->ejercicio,
            'estado_log' => $model->estado_log,
            'estado_log_format' => $model->estado_log_format,
            'estado_log_color' => $model->color_estado_log
        ];
    }
}
