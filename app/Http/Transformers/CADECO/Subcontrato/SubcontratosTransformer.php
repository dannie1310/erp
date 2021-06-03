<?php


namespace App\Http\Transformers\CADECO\Subcontrato;

use App\Models\CADECO\Subcontratos\Subcontratos;
use League\Fractal\TransformerAbstract;

class SubcontratosTransformer extends TransformerAbstract
{
    public function transform(Subcontratos $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'observacion' => $model->observacion,
            'fecha_ini_format' => $model->fecha_inicio_ejecucion_format,
            'fecha_ini_ejec' => $model->fecha_ini_ejec,
            'fecha_fin_format' => $model->fecha_fin_ejecucion_format,
            'fecha_fin_ejec' => $model->fecha_fin_ejec,
        ];
    }
}
