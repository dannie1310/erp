<?php


namespace App\Http\Transformers\CADECO\Compras;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\AsignacionProveedores;

class AsignacionProveedoresTransformer extends TransformerAbstract
{

    public function transform(AsignacionProveedores $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->solicitud->fecha,
            'fecha_solicitud_format' => $model->solicitud->fecha_format,
            'fecha_format' => $model->fecha_format,
            'observaciones' => (string) $model->solicitud->observaciones,
            'estado' => $model->estadoAsignacion->descripcion,
            'folio_solicitud_format' => $model->solicitud->numero_folio_format,
            'opciones' => $model->solicitud->opciones,
            'folio_asignacion_format' => $model->folio_format,
        ];
    }
}
