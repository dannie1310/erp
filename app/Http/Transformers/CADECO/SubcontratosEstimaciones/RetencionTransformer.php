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

class RetencionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(Retencion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion,
            'id_tipo_retencion'=> $model->id_tipo_retencion,
            'tipo_retencion'=> $model->tipo_retencion,
            'importe'=> $model->importe,
            'importe_format'=> $model->importe_format,
            'importe_disponible' => $model->importe_disponible,
            'importe_disponible_format' => $model->importe_disponible_format,
            'concepto'=> $model->concepto,
        ];
    }

    /**
     * @param Retencion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(Retencion $model)
    {
        if($tipo = $model->retencion_tipo)
        {
            return $this->item($tipo, new RetencionTipoTransformer);
        }
        return null;
    }
}
