<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinFacIngresoFacturaConcepto;
use League\Fractal\TransformerAbstract;

class ConceptoTransformer extends TransformerAbstract
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

    public function transform(FinFacIngresoFacturaConcepto $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre' => $model->tipo_ingreso
        ];
    }
}
