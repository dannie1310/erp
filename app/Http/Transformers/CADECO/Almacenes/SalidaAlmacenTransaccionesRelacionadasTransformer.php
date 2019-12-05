<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 12:58 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;

use League\Fractal\TransformerAbstract;

class SalidaAlmacenTransaccionesRelacionadasTransformer extends TransformerAbstract
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

    ];

    public function transform(Array $transacciones)
    {
        return $transacciones;
    }




}