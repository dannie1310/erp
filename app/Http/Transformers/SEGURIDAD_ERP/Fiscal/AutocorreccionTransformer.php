<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ProveedorSATTransformer;
use App\Models\SEGURIDAD_ERP\Fiscal\Autocorreccion;
use League\Fractal\TransformerAbstract;

class AutocorreccionTransformer extends TransformerAbstract
{

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
        'partidas',
        'proveedor'
    ];

    public function transform(Autocorreccion $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha_hora_registro_format
        ];
    }

    /**
     * @param Autocorreccion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(Autocorreccion $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new CFDAutocorreccionTransformer);
        }
        return null;
    }

    /**
     * @param Autocorreccion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(Autocorreccion $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorSATTransformer);
        }
        return null;
    }
}
