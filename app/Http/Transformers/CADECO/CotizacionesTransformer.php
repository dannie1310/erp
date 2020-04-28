<?php


namespace App\Http\Transformers\CADECO;

use App\Models\CADECO\Cotizacion;
use League\Fractal\TransformerAbstract;

class CotizacionesTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'material',
        'moneda'
    ];


    public function transform(Cotizacion $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'precio_unitario' => $model->precio_unitario,
            'precio_unitario_format' => $model->precio_unitario_format,
            'cantidad' => $model->cantidad_format,
            'descuento' => ($model->partida) ? $model->partida->descuento_partida : '-------',
            'precio_total' => $model->precio_total,
            'precio_total_moneda' => $model->precio_total_moneda,
            'observacion' => ($model->partida) ? $model->partida->observaciones : null,
            'no_cotizado' => ($model->no_cotizado == 0) ? true :false
        ];
    }

    /**
     * @param Cotizacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(Cotizacion $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    /**
     * @param Cotizacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(Cotizacion $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param Cotizacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePartida(Cotizacion $model)
    {
        if($partida = $model->partida)
        {
            // return $this->item($partida, new );
        }
    }
}