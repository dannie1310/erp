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
            'rfc' => $model->fecha_hora_registro_format,
            'razon_social' => $model->total_partidas,
            'tipo_persona_format' => $model->tipo_persona_format,
            'fecha_primera_publicacion' => $model->fecha_primera_publicacion,
            'entidad_federativa' => $model->entidad_federativa,
            'estado' => $model->cantidad_partidas
        ];
    }
}
