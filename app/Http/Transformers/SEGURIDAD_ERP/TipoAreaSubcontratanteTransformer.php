<?php


namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\AreaSubcontratante;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use League\Fractal\TransformerAbstract;

class TipoAreaSubcontratanteTransformer extends TransformerAbstract
{
    public function transform(TipoAreaSubcontratante $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->descripcion,
            ];
    }

}