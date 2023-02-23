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
       
    ];

    public function transform(Concurso $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre' => (string) $model->nombre,
            'fecha_format' => $model->fecha_format,
            'estatus' => $model->estado
        ];
    }
}