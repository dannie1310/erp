<?php

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\CADECO\CostoTransformer;
use App\Models\CADECO\Contabilidad\CuentaCosto;
use League\Fractal\TransformerAbstract;

class CuentaCostoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'costo',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'costo',
    ];

    public function transform(CuentaCosto $model){
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => (string) $model->cuenta,
        ];
    }

    /**
     * Include Costo
     * @return \League\Fractal\Resource\Item
     */
    public function includeCosto(CuentaCosto $model)
    {
        if ($costo = $model->costo) {
            return $this->item($costo, new CostoTransformer);
        }
        return null;
    }
}