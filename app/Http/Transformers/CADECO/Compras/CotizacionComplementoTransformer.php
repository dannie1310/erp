<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Models\CADECO\Compras\CotizacionComplemento;
use League\Fractal\TransformerAbstract;

class CotizacionComplementoTransformer extends TransformerAbstract
{
    public function transform(CotizacionComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'descuento_format' => $model->descuento_format,
            'descuento' => $model->descuento,
            'anticipo' => $model->anticipo,
            'anticipo_format' => $model->anticipo_format,
            'parcialidades' => $model->parcialidades,
            'parcialidades_format' => $model->parcialidades_format,
            'dias_credito' => (int) $model->dias_credito,
            'vigencia' => (int) $model->vigencia,
            'entrega' => (int) $model->plazo_entrega,
            'tc_usd_format' => $model->tipo_cambio_usd_format,
            'tc_eur_format' => $model->tipo_cambio_eur_format,
            'tc_usd' => $model->tc_usd,
            'tc_eur' => $model->tc_eur
        ];
    }
}