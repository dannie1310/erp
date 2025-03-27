<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:33 p. m.
 */


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\Compras\SolicitudPartidaComplementoTransformer;
use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Http\Transformers\CADECO\EntregaTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\ItemSolicitudCompra;
use League\Fractal\TransformerAbstract;

class SolicitudCompraPartidaTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'complemento',
        'entrega',
        'material'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'material'
    ];

    public function transform(ItemSolicitudCompra $model)
    {
         return [
             'id' => $model->getKey(),
             'unidad' => $model->unidad,
             'cantidad' => $model->cantidad_decimal,
             'cantidad_format' => $model->cantidad_format,
             'solicitado_cantidad' => $model->solicitado_cantidad_format,
             'orden_compra_cantidad' => $model->cantidad_orden_compra ? $model->cantidad_orden_compra_format : '0.0',
             'surtido_cantidad' => $model->cantidad_entrada_material ? $model->cantidad_entrada_material_format : '0.0',
             'existencia_cantidad' => $model->suma_inventario_format,
             'cantidad_original' => ($model->cantidad_original1 > 0) ? $model->cantidad_original_format : $model->solicitado_cantidad_format,
             'cantidad_original_num' => ($model->cantidad_original1 > 0) ? $model->cantidad_original1 : $model->cantidad,
             'descuento' => $model->descuento
         ];
    }

    /**
     * @param ItemSolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */

    public function includeComplemento(ItemSolicitudCompra $model)
    {
        if($complemento = $model->complemento)
        {
            return $this->item($complemento, new SolicitudPartidaComplementoTransformer);
        }
        return null;
    }


    /**
     * @param ItemSolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */

    public function includeEntrega(ItemSolicitudCompra $model)
    {
        if($entrega = $model->entrega)
        {
            return $this->item($entrega, new EntregaTransformer);
        }
        return null;
    }

    /**
     * @param ItemSolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */

    public function includeMaterial(ItemSolicitudCompra $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }
}
