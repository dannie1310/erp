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
            'registro' => (string) $model->nombre_registro,
            'desactivo' => (string) $model->nombre_desactivo,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
            'motivo' => $model->motivo,
            'fecha_desactivo_format' => $model->fecha_desactivo_format
        ];
    }
}
