<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:42 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\AjustePositivo;
use League\Fractal\TransformerAbstract;

class AjustePositivoTransformer extends TransformerAbstract
{
    public function transform(AjustePositivo $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_format' => $model->fecha_format,
            'observaciones' => $model->observaciones,
            'estado' => $model->estado,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format_orden,
            'referencia' => $model->referencia,
            'estado_format' => $model->estatus
        ];
    }
}