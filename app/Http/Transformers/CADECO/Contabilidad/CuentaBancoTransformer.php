<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/02/2019
 * Time: 04:23 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;

use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Http\Transformers\CuentaBancariaTransformer;
use App\Models\CADECO\Contabilidad\CuentaBanco;
use League\Fractal\TransformerAbstract;

class CuentaBancoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentaBanco',
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo'
    ];

    public function transform(CuentaBanco $model) {
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => (string) $model->cuenta
        ];
    }

    /**
     * Include Cuenta
     * @param CuentaBanco $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentaBanco(CuentaBanco $model)
    {
        if ($cuenta = $model->cuentaContable) {
            return $this->collection($cuenta, new CuentaBancariaTransformer);
        }
        return null;
    }

    /**
     * Include Tipo
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(CuentaBanco $model)
    {
        if($tipo = $model->tipoCuentaContable) {
            return $this->collection($tipo, new TipoCuentaContableTransformer);
        }
        return null;
    }

}