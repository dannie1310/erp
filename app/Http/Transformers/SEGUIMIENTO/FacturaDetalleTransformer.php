<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinFacIngresoFacturaDetalle;
use League\Fractal\TransformerAbstract;

class FacturaDetalleTransformer extends TransformerAbstract
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
        'partida'
    ];

    public function transform(FinFacIngresoFacturaDetalle $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'total' => $model->total,
            'total_format' => $model->total_format,
            'antes_iva' => $model->antes_iva
        ];
    }

    /**
     * @param FinFacIngresoFacturaDetalle $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePartida(FinFacIngresoFacturaDetalle $model)
    {
        if($partida = $model->partida)
        {
            return $this->item($partida, new IngresoPartidaTransformer);
        }
        return null;
    }
}
