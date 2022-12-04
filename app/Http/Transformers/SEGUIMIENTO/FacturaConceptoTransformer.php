<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinFacIngresoFacturaConcepto;
use League\Fractal\TransformerAbstract;

class FacturaConceptoTransformer extends TransformerAbstract
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
        'tipoIngreso'
    ];

    public function transform(FinFacIngresoFacturaConcepto $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'importe' => $model->importe,
            'importe_format' => $model->importe_format
        ];
    }

    /**
     * @param FinFacIngresoFacturaConcepto $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoIngreso(FinFacIngresoFacturaConcepto $model)
    {
        if ($tipo = $model->tipoIngreso)
        {
            return $this->item($tipo, new TipoIngresoTransformer);
        }
        return null;
    }
}
