<?php


namespace App\Http\Transformers\CONCURSOS;

use League\Fractal\TransformerAbstract;

class ConcursoParticipanteInformeTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     * @var array
     */
    protected $availableIncludes = [
    ];

    public function transform($model)
    {
        return [
            'nombre' => (string) $model->nombre,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'es_empresa_hermes' => $model->es_empresa_hermes == 1 ? true : false ,
            'es_ganador' => $model->es_ganador,
            'porcentaje_vs_primer_lugar' => $model->porcentaje_vs_primer_lugar,
            'porcentaje_vs_promedio' => $model->porcentaje_vs_promedio,
            'porcentaje_vs_hermes' => $model->porcentaje_vs_hermes,
            'porcentaje_vs_ganador' => $model->porcentaje_vs_ganador,
            'i' => (string) $model->i
        ];
    }
}
