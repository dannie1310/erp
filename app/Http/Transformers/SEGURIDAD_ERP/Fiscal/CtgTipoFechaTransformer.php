<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgTipoFecha;

class CtgTipoFechaTransformer extends TransformerAbstract
{

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    protected $availableIncludes = [
    ];

    public function transform(CtgTipoFecha $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
        ];
    }

}
