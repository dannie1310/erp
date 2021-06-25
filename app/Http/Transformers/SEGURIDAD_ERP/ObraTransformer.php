<?php


namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Obra;
use League\Fractal\TransformerAbstract;

class ObraTransformer extends TransformerAbstract
{
    public function transform(Obra $model) {
        return [
            'id' => (int) $model->getKey(),
            'nombre' =>(string)$model->nombre,
        ];
    }

}
