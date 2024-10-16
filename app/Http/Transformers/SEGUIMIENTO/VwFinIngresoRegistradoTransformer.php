<?php

namespace App\Http\Transformers\SEGUIMIENTO;

use App\Models\REPSEG\VwFinIngresoRegistrado;
use League\Fractal\TransformerAbstract;

class VwFinIngresoRegistradoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(VwFinIngresoRegistrado $model) {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->fecha,
            'numero' => $model->numero,
            'proyecto' => $model->proyecto,
            'proyecto_tipo' => $model->proyecto_tipo,
            'cuenta_ingresa' => $model->cuenta_ingresa,
            'moneda' => $model->moneda,
            'total' => $model->total,
            'total_format' => $model->total_format
        ];
    }
}
