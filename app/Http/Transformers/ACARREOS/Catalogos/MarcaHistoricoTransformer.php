<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\MarcaHistorico;
use League\Fractal\TransformerAbstract;

class MarcaHistoricoTransformer extends TransformerAbstract
{
    public function transform(MarcaHistorico $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->Descripcion,
            'usuario_registro' => (string) $model->nombre_registro,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado_format,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
        ];
    }
}
