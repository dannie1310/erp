<?php

namespace App\Http\Transformers\CADECO\Presupuesto;
use App\Models\CADECO\PresupuestoObra\DatoConcepto;
use League\Fractal\TransformerAbstract;

class DatoConceptoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'responsables',
    ];

    public function transform(DatoConcepto $model)
    {
        return [
            'id' => $model->getKey(),
            'calificacion' => $model->calificacion,
            'fecha_inicio' => $model->fecha_inicio,
            'fecha_fin' => $model->fecha_fin,
            'revision_diaria' => $model->revision_diaria,
            'revision_semanal' => $model->revision_semanal,
            'revision_mensual' => $model->revision_mensual,
        ];
    }

    public function includeResponsables(DatoConcepto $model)
    {
        if ($responsable = $model->responsables) {
            return $this->collection($responsable, new ConceptoTransformer);
        }
        return null;
    }
}
