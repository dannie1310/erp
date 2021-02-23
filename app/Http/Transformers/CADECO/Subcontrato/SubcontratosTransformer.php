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
            'descripcion' => $model->observacion,
            'fecha_ini' => ($model->fecha_ini_ejec) ? date("d/m/Y", strtotime($model->fecha_ini_ejec)) : '--------',
            'fecha_fin' => ($model->fecha_fin_ejec) ? date("d/m/Y", strtotime($model->fecha_fin_ejec)) : '--------'
        ];
    }
}