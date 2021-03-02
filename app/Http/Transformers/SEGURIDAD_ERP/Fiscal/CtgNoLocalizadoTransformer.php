<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;

class CtgNoLocalizadoTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        
    ];

    protected $availableIncludes = [
    ];

    public function transform(CtgNoLocalizado $model)
    {
        return [
            'id' => $model->getKey(),
            'rfc' => $model->rfc,
            'razon_social' => $model->razon_social,
            'tipo_persona_format' => $model->tipo_persona_format,
            'primera_fecha_publicacion' => $model->primera_fecha_publicacion_format,
            'entidad_federativa' => $model->entidad_federativa,
            'estado' => $model->estado
        ];
    }
}
