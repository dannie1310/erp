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
        'participantes',
        'participantes_informe'
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
            'entidad_licitante' => (string) $model->entidad_licitante,
            'numero_licitacion' => (string) $model->numero_licitacion,
            'fecha_format' => $model->fecha_format,
            'fecha' => $model->fecha,
            'fecha_fallo_format' => $model->fecha_fallo_format,
            'estatus_descripcion' => $model->estado,
            'estatus' => $model-> estatus,
            'estatus_color' => $model->estado_color,
            'estado_fallo' => $model->estado_fallo_txt,
            'estado_apertura' => $model->estado_apertura,
            'resultado_apertura' => $model->resultado_txt,
            'resultado_fallo' => $model->resultado_fallo_txt,
            'color_estado_fallo'=>$model->color_estado_fallo,
            'color_estado_apertura'=>$model->color_estado_apertura,
        ];
    }

    public function includeParticipantes(Concurso $model)
    {
        if($participantes = $model->participantes()->orderBy("lugar")->get())
        {
            return $this->collection($participantes, new ConcursoParticipanteTransformer);
        }
        return null;
    }
    public function includeParticipantesInforme(Concurso $model)
    {
        if($participantes = $model->participantes_para_informe)
        {
            return $this->collection($participantes, new ConcursoParticipanteInformeTransformer);
        }
        return null;
    }
}
