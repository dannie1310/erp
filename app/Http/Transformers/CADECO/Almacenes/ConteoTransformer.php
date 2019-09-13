<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\Conteo;
use League\Fractal\TransformerAbstract;

class ConteoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'marbete'
    ];

    public function transform(Conteo $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_conteo' => $model->tipo_conteo,
            'cantidad_usados' => $model->cantidad_usados,
            'cantidad_nuevo' => $model->cantidad_nuevo,
            'cantidad_inservible' => $model->cantidad_inservible,
            'total' => $model->total,
            'iniciales' => $model->iniciales,
            'observaciones' => $model->observaciones
        ];
    }

    public function includeMarbete(Conteo $model){
        if($marbete = $model->marbete){
            return $this->item($marbete, new MarbeteTransformer);
        }
        return null;
    }

}