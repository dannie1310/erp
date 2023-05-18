<?php


namespace App\Http\Transformers\CONCURSOS;

use App\Models\SEGURIDAD_ERP\Concursos\ConcursoParticipante;
use League\Fractal\TransformerAbstract;

class ConcursoParticipanteTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     * @var array
     */
    protected $availableIncludes = [
    ];

    public function transform(ConcursoParticipante $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre' => (string) $model->nombre,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'es_empresa_hermes' => $model->es_hermes,
            'es_ganador' => $model->es_ganador == 1 ? true : false
        ];
    }
}
