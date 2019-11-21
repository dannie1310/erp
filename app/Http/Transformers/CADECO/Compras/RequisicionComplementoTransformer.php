<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/11/2019
 * Time: 02:05 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgTipoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\TipoAreaCompradoraTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\TipoAreaSolicitanteTransformer;
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
        'area_compradora',
        'area_solicitante',
        'tipo'
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

    /**
     * @param RequisicionComplemento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAreaCompradora(RequisicionComplemento $model)
    {
        if($area = $model->areaCompradora)
        {
            return $this->item($area, new TipoAreaCompradoraTransformer);
        }
        return null;
    }

    /**
     * @param RequisicionComplemento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAreaSolicitante(RequisicionComplemento $model)
    {
        if($area = $model->areaSolicitante)
        {
            return $this->item($area, new TipoAreaSolicitanteTransformer);
        }
        return null;
    }

    /**
     * @param RequisicionComplemento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(RequisicionComplemento $model)
    {
        if($tipo = $model->tipo)
        {
            return $this->item($tipo, new CtgTipoTransformer);
        }
        return null;
    }
}