<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinDimIngresoCliente;
use League\Fractal\TransformerAbstract;

class ClienteTransformer extends TransformerAbstract
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

    public function transform(FinDimIngresoCliente $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre' => $model->cliente
        ];
    }
}
