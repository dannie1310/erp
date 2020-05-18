<?php


namespace App\Http\Transformers\CADECO\Contrato;

use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Models\CADECO\PresupuestoContratistaPartida;
use League\Fractal\TransformerAbstract;

class PresupuestoContratistaPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'concepto'
    ];

    public function transform(PresupuestoContratistaPartida $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'precio_unitario' => $model->precio_unitario,
            'precio_unitario_format' => $model->precio_unitario_format,
            'id_moneda' => (int) $model->IdMoneda
        ];
    }

    /**
     * @param PresupuestoContratistaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto(PresupuestoContratistaPartida $model)
    {
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ContratoTransformer);
        }
        return null;
    }
}