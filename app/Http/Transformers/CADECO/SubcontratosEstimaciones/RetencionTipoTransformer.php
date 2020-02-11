<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 11/02/2020
 * Time: 11:15 AM
 */

namespace App\Http\Transformers\CADECO\SubcontratosEstimaciones;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\SubcontratosEstimaciones\RetencionTipo;

class RetencionTipoTransformer extends TransformerAbstract
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

    public function transform(RetencionTipo $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_retencion' => $model->tipo_retencion,
        ];
    }
}