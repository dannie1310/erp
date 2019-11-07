<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 28/10/2019
 * Time: 07:21 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Entrega;
use League\Fractal\TransformerAbstract;

class EntregaTransformer extends TransformerAbstract
{
    public function transform(Entrega $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_entrega' => $model->numero_entrega,
            'surtida' => $model->surtida,
            'cantidad' => $model->cantidad,
            'pendiente' => $model->pendiente_entrega,
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'id_concepto' => $model->id_concepto,
            'id_almacen' => $model->id_almacen
        ];
    }
}



    /**
     * @param Entrega $model
     * @return \League\Fractal\Resource\Item|null
     */

    public function includeAlmacen(Entrega $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    public function includeConcepto(Entrega $model)
    {
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }

}
