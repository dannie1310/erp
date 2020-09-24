<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 02:03 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Cambio;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\MonedaTransformer;

class CambioTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'moneda'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Cambio $model){
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha,
            'cambio' => $model->cambio,
            'cambio_format' => $model->cambio_format,
            'cambio_formato' => $model->cambio_formato
        ];
    }

    public function includeMoneda(Cambio $model)
    {
        if ($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }
}
