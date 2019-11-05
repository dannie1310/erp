<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Compras\Asignacion;
use League\Fractal\TransformerAbstract;

class AsignacionTransformer extends TransformerAbstract
{

    public function transform(Asignacion $model)
    {
        var_dump($model->fecha_format,$model->observaciones);
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'observaciones' => (string) $model->observaciones,
            'estado' => $model->estado_format,
            'folio' => $model->numero_folio,
            'opciones' => $model->opciones,
            'folio_format' => $model->numero_folio_format_orden,
        ];
    }
}
