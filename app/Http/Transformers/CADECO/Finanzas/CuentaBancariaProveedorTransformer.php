<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/05/2019
 * Time: 05:31 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\Finanzas\CuentaBancariaProveedor;
use League\Fractal\TransformerAbstract;

class CuentaBancariaProveedorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'banco'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(CuentaBancariaProveedor $model)
    {
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta_clabe,
            'sucursal' => $model->sucursal,
            'tipo' => $model->tipo,
            'fecha' => $model->FechaHoraRegistro
        ];
    }

    public function includeEmpresa(CuentaBancariaProveedor $model)
    {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    public function includeBanco(CuentaBancariaProveedor $model)
    {
        if ($empresa = $model->banco) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }
}