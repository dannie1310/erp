<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/02/2019
 * Time: 07:03 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Banco;
use App\Http\Transformers\CADECO\CuentaBancariaTransformer;
use League\Fractal\TransformerAbstract;

class BancoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentaBancaria'
    ];

    public function transform(Banco $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social
        ];
    }

    /**
     * Include CuentaBancaria
     * @param Banco $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentaBancaria(Banco $model)
    {
        if ($cuentas = $model->cuentaBancaria) {
            return $this->collection($cuentas, new CuentaBancariaTransformer);
        }
        return null;
    }

}