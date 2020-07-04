<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ProveedorSATTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEstadosEfosTransformer;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use League\Fractal\TransformerAbstract;

class EfosTransformer extends TransformerAbstract
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
        'proveedor',
        'estatus'
    ];

    public function transform(EFOS $model)
    {
        return [
            'id' => $model->getKey(),
            'rfc' => $model->rfc,
            'razon_social' => $model->razon_social
        ];
    }

    /**
     * @param EFOS $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(EFOS $model)
    {
        if($prov = $model->proveedor)
        {
            return $this->item($prov, new ProveedorSATTransformer);
        }
        return null;
    }

    /**
     * @param EFOS $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEstatus(EFOS $model)
    {
        if($estado = $model->efoEstado)
        {
            return $this->item($estado, new CtgEstadosEfosTransformer);
        }
        return null;
    }
}
