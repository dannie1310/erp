<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 10/02/2020
 * Time: 17:55 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosEstimaciones;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;

class RetencionLiberacionTransformer extends TransformerAbstract
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

    public function transform(Liberacion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion,
            'importe'=> $model->importe,
            'importe_format'=> $model->importe_format,
            'concepto'=> $model->concepto,
        ];
    }
}