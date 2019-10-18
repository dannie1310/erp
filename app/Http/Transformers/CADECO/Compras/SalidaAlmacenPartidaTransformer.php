<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\SalidaAlmacenPartida;
use League\Fractal\TransformerAbstract;

class SalidaAlmacenPartidaTransformer extends TransformerAbstract
{
    /**
 * List of resources possible to include
 * @var array
 */
    protected $availableIncludes = [
        'inventario',
        'movimiento',
        'almacen',
        'concepto',
        'contratista',
        'material'
    ];

    public function transform(SalidaAlmacenPartida $model)
    {

        return [
            'id' => (int)$model->getKey(),
            'unidad' => $model->unidad,
            'cantidad' => $model->cantidad,
            'cantidad_material' => $model->cantidad_material,
            'cantidad_format' => $model->cantidad_format,
            'cantidad_decimal' => $model->cantidad_decimal,
            'saldo' => $model->saldo
        ];
    }

    /**
     * Include Almacen
     * @param SalidaAlmacenPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAlmacen(SalidaAlmacenPartida $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    public function includeInventario(SalidaAlmacenPartida $model)
    {
        if($inventario = $model->inventario)
        {
            return $this->item($inventario, new InventarioTransformer);
        }
        return null;
    }

    /**
     * Include Material
     * @param SalidaAlmacenPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(SalidaAlmacenPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    public function includeConcepto(SalidaAlmacenPartida $model)
    {
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }


    public function includeContratista(SalidaAlmacenPartida $model)
    {
        if($contratista = $model->contratista)
        {
            return $this->item($contratista, new ItemContratistaTransformer);
        }
        return null;
    }

    public function includeMovimiento(SalidaAlmacenPartida $model)
    {
        if($movimiento = $model->movimiento)
        {
            return $this->item($movimiento, new MovimientoTransformer);
        }
        return null;
    }
}
