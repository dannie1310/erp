<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ProveedorSATTransformer;
use App\Models\SEGURIDAD_ERP\Fiscal\NoDeducido;
use League\Fractal\TransformerAbstract;

class NoDeducidoTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'estatus'
    ];

    protected $availableIncludes = [
        'partidas',
        'proveedor',
        'efo',
        'estatus'
    ];

    public function transform(NoDeducido $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha_hora_registro_format,
            'total_partidas' => $model->total_partidas
        ];
    }

    /**
     * @param NoDeducido $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(NoDeducido $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new CFDNoDeducidoTransformer);
        }
        return null;
    }

    /**
     * @param NoDeducido $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(NoDeducido $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorSATTransformer);
        }
        return null;
    }

    /**
     * @param NoDeducido $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEfo(NoDeducido $model)
    {
        if($efo = $model->efo)
        {
            return $this->item($efo, new EfosTransformer);
        }
        return null;
    }

    /**
     * @param NoDeducido $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEstatus(NoDeducido $model)
    {
        if($estatus = $model->ctgEstado)
        {
            return $this->item($estatus, new CtgEstadosCFDTransformer);
        }
        return null;
    }
}
