<?php


namespace App\Http\Transformers\Auxiliares;


use League\Fractal\TransformerAbstract;

class RelacionTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model["id"],
            'tipo' => $model["tipo"],
            'tipo_numero' => $model["tipo_numero"],
            'numero_folio' => $model["numero_folio"],
            'icono' => $model["icono"],
            'fecha_hora' => $model["fecha_hora"],
            'usuario' => $model["usuario"],
            'observaciones' => $model["observaciones"],
            'hora' => $model["hora"],
            'fecha' => $model["fecha"],
        ];
    }

}
