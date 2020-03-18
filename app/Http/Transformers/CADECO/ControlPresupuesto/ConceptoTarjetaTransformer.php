<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 13/03/2020
 * Time: 03:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Models\CADECO\ControlPresupuesto\ConceptoTarjeta;

class ConceptoTarjetaTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'concepto'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(ConceptoTarjeta $model) {
        return [
            'id' => (int) $model->getKey(),
            'id_concepto' => (int) $model->id_concepto,
            'concepto' => $model->conceptoData(),
        ];
    }

    /**
     * @param ConceptoTarjeta $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto(ConceptoTarjeta $model)
    {
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }
}