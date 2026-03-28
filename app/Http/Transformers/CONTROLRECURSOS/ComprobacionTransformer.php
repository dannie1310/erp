<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use League\Fractal\TransformerAbstract;

class ComprobacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'solicitud'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'documentos',
    ];

    public function transform(Comprobacion $model){
        return [
            'id' => $model->getKey(),
            'fecha' => $model->Fecha,
            'fecha_format' => $model->fecha_format,
            'del' => $model->Del,
            'del_format' => $model->fecha_del_format,
            'del_editar' => $model->fecha_del_editar,
            'al' => $model->Al,
            'al_format' => $model->fecha_al_format,
            'al_editar' => $model->fecha_al_editar,
            'ubicacion' => $model->Ubicacion,
            'motivo' => $model->Motivo,
            'serie' => $model->Serie,
            'folio' => $model->Folio,
            'folio_format' => $model->folio_format,
            'solicita' => $model->Solicita,
            'alias_depto' => $model->Alias_Depto,
            'version' => $model->Version,
            'proyecto_descripcion' => $model->proyecto_descripcion,
            'empresa_descripcion' => $model->empresa_descripcion,
            'empleado_descripcion' => $model->empleado_descripcion,
            'departamento' => $model->departamento,
            'folio_solicitud' => $model->folio_solicitud,
            'importe' => $model->ImpSol,
            'importe_format' => $model->importe_format,
            'imp_doctos' => $model->ImpDoctos,
            'imp_doctos_format' => $model->imp_doctos_format,
            'saldo_comp' => $model->saldo_comp,
            'saldo_comp_format' => $model->saldo_comp_format,
            'estado' => $model->IdEstatus,
            'estado_format' => $model->estado_format,
            'estatus' => $model->Estatus,
            'estado_color' => $model->estado_color
        ];
    }

    /**
     * @param Comprobacion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDocumentos(Comprobacion $model)
    {
        if($documentos = $model->documentos)
        {
            return $this->collection($documentos, new DoctoCompTransformer);
        }
        return null;
    }

    /**
     * @param Comprobacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitud(Comprobacion $model)
    {
        if($solicitud = $model->solicitud)
        {
            return $this->item($solicitud, new SolicitudComprobacionTransformer);
        }
        return null;
    }
}
