<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\InventarioFisico;
use League\Fractal\TransformerAbstract;

class InventarioFisicoTransformer extends TransformerAbstract
{

    public function transform(InventarioFisico $model)
    {
        return [
            'id' => $model->getKey(),
            'id_tipo' => $model->id_tipo,
            'folio' => $model->folio,
            'estado' => $model->estado,
            'fecha_hora_inicio' => $model->fecha_hora_inicio,
            'usuario_inicia' => $model->usuario_inicia,
            'fecha_hora_cierre' => $model->fecha_hora_cierre,
            'usuario_cierre' => $model->usuario_cierre,
        ];
    }

}