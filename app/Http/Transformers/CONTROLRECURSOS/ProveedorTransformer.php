<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CONTROL_RECURSOS\Proveedor;
use League\Fractal\TransformerAbstract;

class ProveedorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentas',
        'usuario'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Proveedor $model){
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->RazonSocial,
            'rfc' => $model->RFC
        ];
    }

    /**
     * @param Proveedor $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentas(Proveedor $model)
    {
        if($cuentas = $model->cuentasProveedores)
        {
            return $this->collection($cuentas, new CuentaProveedorTransformer);
        }
        return null;
    }

    /**
     * @param Proveedor $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(Proveedor $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}
