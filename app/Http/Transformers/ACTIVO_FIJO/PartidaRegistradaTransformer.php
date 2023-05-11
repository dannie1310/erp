<?php

namespace App\Http\Transformers\ACTIVO_FIJO;

use App\Models\SCI\VwPartidaRegistrada;
use League\Fractal\TransformerAbstract;

class PartidaRegistradaTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [

    ];

    public function transform(VwPartidaRegistrada $model)
    {
        return [
            'id' => $model->getKey(),
        ];
    }

}
