<?php


namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaCostoTransformer;
use App\Models\CADECO\Costo;
use League\Fractal\TransformerAbstract;

class CostoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'hijos',
        'cuenta'
    ];

    public function transform(Costo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'observaciones' => $model->observaciones,
            'tiene_hijos' => $model->tiene_hijos,

        ];
    }

    /**
     * Include Hijos
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeHijos(Costo $model)
    {
        if ($hijos = $model->hijos) {
            return $this->collection($hijos, new CostoTransformer);
        }
        return null;
    }

    /**
     * Include CuentaCosto
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeCuenta(Costo $model)
    {
        if ($cuenta = $model->cuenta) {
            return $this->item($cuenta, new CuentaCostoTransformer);
        }
        return null;
    }
}