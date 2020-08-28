<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 09/06/2020
 * Time: 04:16 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Models\CADECO\Compras\OrdenCompraPartidaComplemento;

class OrdenCompraPartidaComplementoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];


    public function transform(OrdenCompraPartidaComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'descuento'=> $model->descuento,
            'id_moneda' => $model->id_moneda,
        ];
    }


}