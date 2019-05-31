<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 12:33 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Finanzas\BancoComplementoTransformer;
use App\Models\CADECO\Banco;
use League\Fractal\TransformerAbstract;

class BancoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
       'complemento'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'complemento'
    ];

    public function transform(Banco $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social
        ];
    }

    /**
     * @param Banco $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeComplemento(Banco $model)
    {
        if($banco = $model->complemento){
            return $this->item($banco, new BancoComplementoTransformer);
        }
        return null;
    }
}