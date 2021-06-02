<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\EmpresaHistorico;
use League\Fractal\TransformerAbstract;

class EmpresaHistoricoTransformer extends TransformerAbstract
{
    public function transform(EmpresaHistorico $model) {
        return [
            'id' => (int) $model->getKey(),
            'razon_social' => (string) $model->razonSocial,
            'rfc' => $model->RFC,
            'fecha_registro' => $model->fecha_registro,
            'fecha_desactivo' => $model->fecha_desactivacion_format,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'nombre_registro' => (string) $model->nombre_registro,
            'nombre_desactivo' => (string) $model->nombre_desactivo,
            'motivo' => $model->motivo
        ];
    }
}
