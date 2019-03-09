<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 08:31 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Subcontrato;
use League\Fractal\TransformerAbstract;

class SubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'estimaciones'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Subcontrato $model)
    {
        return [
            'id' => $model->getKey(),
            'referencia' => $model->referencia
        ];
    }

    public function includeEstimaciones(Subcontrato $model)
    {
        if($estimaciones = $model->estimaciones){
            return $this->collection($estimaciones, new EstimacionTransformer);
        }
        return null;
    }
}