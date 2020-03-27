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
            'fecha_ini' => date_format(date_create($model->fecha_ini_ejec),"d/m/Y"),
            'fecha_fin' => date_format(date_create($model->fecha_fin_ejec),"d/m/Y")
        ];
    }
}