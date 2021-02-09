<?php
namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\PolizaCFDI;
use League\Fractal\TransformerAbstract;

class PolizaCFDITransformer extends TransformerAbstract
{

    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [

    ];

    public function transform(PolizaCFDI $model) {
        return [
            'id' => (int) $model->id,
            'base_datos'=>$model->base_datos_contpaq,
            'ejercicio'=>$model->ejercicio,
            'periodo'=>$model->periodo,
            'folio'=>$model->folio,
            'tipo'=>$model->tipo,
        ];
    }
}
