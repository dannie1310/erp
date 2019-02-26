<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/02/2019
 * Time: 04:23 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;

use App\Http\Transformers\CADECO\CuentaTransformer;
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
        'tipo',
        'cuentaContable'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo',
        'cuentaContable'
    ];

    public function transform(CuentaBanco $model) {
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => (string) $model->cuenta
        ];
    }

    /**
     * Include Tipo
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(CuentaBanco $model)
    {
        $tipo = $model->tipoCuentaContable;
        return $this->item($tipo, new TipoCuentaContableTransformer);
    }

    /**
     * Include Cuenta
     * @return \League\Fractal\Resource\Item
     */
    public function includeCuentaContable(CuentaBanco $model)
    {
        $cuenta = $model->cuentaContable;
        return $this->item($cuenta, new CuentaTransformer);
    }


}