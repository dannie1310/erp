<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/11/2019
 * Time: 02:05 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Compras\RequisicionComplemento;
use League\Fractal\TransformerAbstract;

class RequisicionComplementoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
       'area_compradora'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(RequisicionComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'folio' => $model->folio_compuesto,
            'concepto' => $model->concepto
        ];
    }

    public function includeAreaCompradora(RequisicionComplemento $model)
    {
        if($area = $model->areaCompradora)
        {
            return $this->item();
        }
    }
}