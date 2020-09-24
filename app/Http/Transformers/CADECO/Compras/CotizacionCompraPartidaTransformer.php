<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Models\CADECO\CotizacionCompraPartida;
use League\Fractal\TransformerAbstract;

class CotizacionCompraPartidaTransformer extends TransformerAbstract
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

    /**
     * ist of resources include
     * @var array
     */
    protected $defaultIncludes = [
        'material',
        'moneda'
    ];

    public function transform(CotizacionCompraPartida $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'precio_unitario' => $model->precio_unitario,
            'precio_unitario_format' => $model->precio_unitario_format,
            'cantidad' => $model->cantidad,
            'cantidad_format' => $model->cantidad_format,
            'descuento' => ($model->partida) ? $model->partida->descuento_partida : '-------',
            'porcentaje_descuento' => ($model->partida) ? $model->partida->descuento_partida : 0,
            'precio_total' => $model->precio_total,
            'precio_total_moneda' => $model->precio_total_moneda,
            'observacion' => ($model->partida) ? $model->partida->observaciones : null,
            'no_cotizado' => ($model->no_cotizado == 0) ? true :false,
            'id_moneda' => (int) $model->id_moneda,
            'id_item_solicitud' => (int) $model->item_solicitud,
        ];
    }

    /**
     * @param CotizacionCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(CotizacionCompraPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    /**
     * @param CotizacionCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(CotizacionCompraPartida $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }
}
