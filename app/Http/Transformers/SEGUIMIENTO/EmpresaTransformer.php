<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinDimIngresoEmpresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
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

    public function transform(FinDimIngresoEmpresa $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre' => $model->empresa
        ];
    }
}
