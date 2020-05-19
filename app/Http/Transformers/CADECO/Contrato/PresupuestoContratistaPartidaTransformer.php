<?php


namespace App\Http\Transformers\CADECO\Contrato;

use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
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
        'concepto',
        'moneda'
    ];

    public function transform(PresupuestoContratistaPartida $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'precio_unitario' => $model->precio_unitario,
            'precio_unitario_format' => $model->precio_unitario_format,
            'id_moneda' => (int) $model->IdMoneda,
            'descuento' => $model->PorcentajeDescuento,
            'precio_total' => $model->precio_total
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

    /**
     * @param PresupuestoContratistaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(PresupuestoContratistaPartida $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }
}