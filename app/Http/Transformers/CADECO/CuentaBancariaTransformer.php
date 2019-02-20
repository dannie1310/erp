<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/02/2019
 * Time: 01:52 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Cuenta;
use App\Http\Transformers\CADECO\Contabilidad\CuentaBancoTransformer;
use League\Fractal\TransformerAbstract;

class CuentaBancariaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentas',
        'bancos'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'cuentas',
        'bancos'
    ];

    public function transform(Cuenta $model)
    {
        return [
            'id' => $model->getKey(),
            'numero' => $model->numero,
            'abreviatura' => $model->abreviatura,
            'banco' => $model->banco->razon_social,
        ];
    }

    /**
     * Include CuentaBanco
     * @param Cuenta $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentas(Cuenta $model){
        if ($cuentas = $model->cuentaBanco) {
            return $this->collection($cuentas, new CuentaBancoTransformer);
        }
        return null;
    }

    public function includeBancos(Cuenta $model)
    {
        if ($cuenta = $model->banco) {
            return $this->item($cuenta, new BancoTransformer);
        }
        return null;

    }
}