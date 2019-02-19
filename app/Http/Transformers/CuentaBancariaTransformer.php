<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/02/2019
 * Time: 07:18 PM
 */

namespace App\Http\Transformers;


use App\Http\Transformers\CADECO\BancoTransformer;
use App\Models\CADECO\Cuenta;
use League\Fractal\TransformerAbstract;

class CuentaBancariaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'banco'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Cuenta $model)
    {
        return [
            'id' => $model->getKey(),
            'numero' => $model->numero,
            'abreviatura' => $model->abreviatura
        ];
    }

    /**
     * Include Banco
     * @param Cuenta
     * @return \League\Fractal\Resource\Item
     */
    public function includeBanco(Cuenta $model)
    {
        if($banco = $model->banco){
            return $this->item($banco, new BancoTransformer);
        }
        return null;
    }
}