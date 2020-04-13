<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\CotizacionCompra;
use League\Fractal\TransformerAbstract;

class CotizacionTransformer extends TransformerAbstract
{
    public function transform(CotizacionCompra $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'referencia' => (string)$model->referencia,
            'observaciones' => (string)$model->observaciones,
            'estado' => (int) $model->estado,
            'estado_format' => $model->estado_format,
            'folio' => $model->numero_folio,
            'operacion' => $model->operacion,
            'opciones' => $model->opciones,
            'folio_format' => $model->numero_folio_format,
            'usuario_registro' => ($model->id_usuario) ? $model->id_usuario : '--------------'
        ];
    }

}