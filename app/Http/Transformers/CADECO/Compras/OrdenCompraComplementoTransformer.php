<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Compras;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\OrdenCompraComplemento;

class OrdenCompraComplementoTransformer extends TransformerAbstract
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

    public function transform(OrdenCompraComplemento $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'plazos_entrega_ejecucion' => $model->plazos_entrega_ejecucion,
            'estatus' => $model->estatus,
            'id_forma_pago' => $model->id_forma_pago,
            'id_forma_pago_credito' => $model->id_forma_pago_credito,
            'id_tipo_credito' => $model->id_tipo_credito,
            'domicilio_entrega' => $model->domicilio_entrega,
            'otras_condiciones' => $model->otras_condiciones,
            'fecha_entrega' => $model->fecha_entrega,
            'con_fianza' => $model->con_fianza,
            'id_tipo_fianza' => $model->id_tipo_fianza,
            'registro' => $model->registro,
            'timestamp_registro' => $model->timestamp_registro,
            'id_asignacion_proveedor' => $model->id_asignacion_proveedor,
        ];
    }

}
