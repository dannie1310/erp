<?php


namespace App\Http\Transformers\CADECO\SubcontratosCM;

use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Models\CADECO\SubcontratosCM\Solicitud;
use League\Fractal\TransformerAbstract;

class SolicitudTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontrato',
        'items',
        'relaciones'
    ];

    /**
     * @param Solicitud $model
     * @return array
     */
    public function transform(Solicitud $model)
    {
        return [
            'id' => $model->getKey(),
            'id_subcontrato' => $model->id_antecedente,
            'fecha_format' => $model->fecha_format,
            'fecha' => $model->fecha,
            'fecha_registro_format' => $model->fecha_registro_format,
            'fecha_registro' => $model->fecha_registro,
            'impuesto' => $model->impuesto,
            'impuesto_format' => $model->impuesto_format,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'usuario_registro' => $model->usuario_registro,
            'fecha_aplicacion' => $model->fecha_aplicacion,
            'fecha_aplicacion_format' => $model->fecha_aplicacion_format,
            'usuario_aplico' => $model->usuario_aplico,
            'numero_folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'observaciones' => $model->observaciones,
            'estado_descripcion' => $model->estado_descripcion,
        ];
    }

    /**
     * @param Solicitud $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(Solicitud $model) {
        if ($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param Solicitud $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeItems(Solicitud $model)
    {
        if ($partidas = $model->partidas) {
            return $this->collection($partidas, new PartidaTransformer);
        }
        return null;
    }

    public function includeRelaciones(Solicitud $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }
}
