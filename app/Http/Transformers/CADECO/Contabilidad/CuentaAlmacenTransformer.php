<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 07:01 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Models\CADECO\Contabilidad\CuentaAlmacen;
use League\Fractal\TransformerAbstract;

class CuentaAlmacenTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'almacen'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'almacen'
    ];

    public function transform(CuentaAlmacen $model) {
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => (string) $model->cuenta
        ];
    }

    /**
     * Include Almacen
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeAlmacen(CuentaAlmacen $model)
    {
        $almacen = $model->almacen;

        return $this->item($almacen, new AlmacenTransformer);
    }
}