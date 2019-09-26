<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\LayoutPago;
use League\Fractal\TransformerAbstract;

class CargaLayoutPagoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(LayoutPago $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_registro' => date('Y-m-d H:i:s', strtotime($model->fecha_hora_carga)),
            'estado' => $model->estado,
        ];
    }
}