<?php



namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Unidad;
use League\Fractal\TransformerAbstract;

class UnidadTransformer extends TransformerAbstract
{

    public function transform(Unidad $model)
    {
        return [
            'unidad' => $model->unidad,
            'tipo_unidad' => $model->tipo_unidad,
            'descripcion' => $model->descripcion
        ];
    }
}
