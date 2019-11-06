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
            'cantidad_nuevo' => number_format($model->cantidad_nuevo,2),
            'nuevo' => $model->nuevo,
            'cantidad_inservible' => number_format($model->cantidad_inservible,2, ".",","),
            'inservible' => number_format($model->inservible,2,".",","),
            'total' => number_format($model->total,2,".",","),
            'total_format' => number_format($model->total_format,2,".",","),
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