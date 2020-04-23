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
            'folio_solicitud' => $model->solicitud->numero_folio_format,
            'folio_cotizacion' => $model->folio_format,
            'concepto' => $model->solicitud->concepto,
            'fecha_format' => $model->timestamp_registro,
            'estado' => $model->estado->descripcion,
        ];
    }
}
