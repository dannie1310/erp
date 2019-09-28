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
            'tipo_conteo_format' => $model->tipo_conteo_format,
            'folio_marbete' => $model->folio_marbete,
            'cantidad_usados' => $model->cantidad_usados,
            'usado' => $model->usado,
            'cantidad_nuevo' => $model->cantidad_nuevo,
            'nuevo' => $model->nuevo,
            'cantidad_inservible' => $model->cantidad_inservible,
            'inservible' => $model->inservible,
            'total' => $model->total,
            'total_format' => $model->total_format,
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