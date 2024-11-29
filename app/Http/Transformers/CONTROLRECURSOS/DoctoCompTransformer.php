<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\DoctoComp;
use League\Fractal\TransformerAbstract;

class DoctoCompTransformer extends TransformerAbstract
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

    public function transform(DoctoComp $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_docto' => $model->FechaDocto,
            'tipo_docto_comp' => $model->TipoDoctoComp,
            'folio_docto' => $model->FolioDocto,
            'tipo_gasto' => $model->Concepto,
            'importe' => $model->Importe,
            'iva' => $model->IVA,
            'imp_hosp' => $model->ImpHosp,
            'total' => $model->Total,
            'observaciones' => $model->Observaciones,
            'clasificacion' => $model->Clasificacion,
            'serie_vd' => $model->SerieVD,
            'folio_vd' => $model->FolioVD,
            'fecha_editar' => $model->fecha_editar,
            'uuid' => $model->uuid
        ];
    }
}
