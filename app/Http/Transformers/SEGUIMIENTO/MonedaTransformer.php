<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\GrlMoneda;
use League\Fractal\TransformerAbstract;

class MonedaTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [
    ];

    public function transform(GrlMoneda $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre' => $model->moneda
        ];
    }
}
