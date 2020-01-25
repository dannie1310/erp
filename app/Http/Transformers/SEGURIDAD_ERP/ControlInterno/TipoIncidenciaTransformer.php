<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\ControlInterno;


use App\Models\SEGURIDAD_ERP\ControlInterno\TipoIncidencia;
use League\Fractal\TransformerAbstract;

class TipoIncidenciaTransformer extends TransformerAbstract
{
    public function transform(TipoIncidencia $model) {
        return [
            'id' => (int) $model->getKey(),
            'description' => (string) $model->description
        ];
    }
}
