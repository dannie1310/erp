<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 06:09 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use League\Fractal\TransformerAbstract;

class SolicitudMovimientoFondoGarantiaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    /*protected $availableIncludes = [
        'fondo_garantia',
        'movimientos',
        'tipo'
    ];*/

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    /*protected $defaultIncludes = [
        'almacen'
    ];*/

    /**
     * @param SolicitudMovimientoFondoGarantia $model
     * @return array
     */
    public function transform(SolicitudMovimientoFondoGarantia $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha' => (string)$model->fecha,
            'referencia' => (string)$model->referencia,
            'importe' => (string)$model->importe,
            'observaciones' => (string)$model->observaciones,
            'usuario_registra'=>(string)$model->usuario_registra,
            'usuario_registra_desc'=>(string)$model->usuario_registra_desc,
            'estado_desc'=>(string)$model->estado_desc,
            'created_at'=>(string)$model->created_at,
        ];
    }

    /**
     * Include Almacen
     *
     * @return \League\Fractal\Resource\Item
     */
    /*public function includeAlmacen(CuentaAlmacen $model)
    {
        $almacen = $model->almacen;

        return $this->item($almacen, new AlmacenTransformer);
    }*/
}