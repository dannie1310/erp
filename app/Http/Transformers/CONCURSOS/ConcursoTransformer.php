<?php


namespace App\Http\Transformers\CONCURSOS;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use League\Fractal\TransformerAbstract;

class ConcursoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     * @var array
     */
    protected $availableIncludes = [
        'participantes'
    ];

    /**
     * ist of resources include
     * @var array
     */
    protected $defaultIncludes = [
        'participantes'
    ];

    public function transform(Concurso $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre' => (string) $model->nombre,
            'fecha_format' => $model->fecha_format,
            'estatus_descripcion' => $model->estado,
            'estatus' => $model-> estatus,
            'estatus_color' => $model->estado_color
        ];
    }

    public function includeParticipantes(Concurso $model)
    {
        if($participantes = $model->participantes)
        {
            return $this->collection($participantes, new ConcursoParticipanteTransformer);
        }
        return null;
    }
}