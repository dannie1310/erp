<?php


namespace App\Http\Transformers\CADECO\Contrato;

use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\EstadoLabelTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\SubcontratosCM\PartidaTransformer;
use App\Http\Transformers\CADECO\SubcontratosCM\SolicitudCambioSubcontratoComplementoTransformer;
use App\Models\CADECO\SolicitudCambioSubcontrato;
use App\Models\CADECO\Subcontrato;
use League\Fractal\TransformerAbstract;

class SolicitudCambioSubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontrato',
        'partidas',
        'relaciones',
        'empresa',
        'moneda',
        'tipo',
        'complemento'
    ];

    protected $defaultIncludes = [
        'estado_label'
    ];

    /**
     * @param SolicitudCambioSubcontrato $model
     * @return array
     */
    public function transform(SolicitudCambioSubcontrato $model)
    {
        return [
            'id' => $model->getKey(),
            'id_subcontrato' => $model->id_antecedente,
            'fecha_format' => $model->fecha_format,
            'fecha' => $model->fecha,
            'fecha_registro_format' => $model->fecha_registro_format,
            'fecha_hora_registro_format' => $model->fecha_hora_registro_format,
            'fecha_registro' => $model->fecha_registro,
            'impuesto' => $model->impuesto,
            'impuesto_format' => $model->impuesto_format,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'porcentaje_cambio_format' => $model->porcentaje_cambio_format,
            'porcentaje_cambio' => $model->porcentaje_cambio,
            'usuario_registro' => $model->usuario_registro,
            'numero_folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'observaciones' => $model->observaciones,
            'estado_descripcion' => $model->estado_descripcion,
            'estado' => $model->estado,
            'monto_original_subcontrato_format' => $model->subcontratoOriginal->monto_format,
        ];
    }

    /**
     * @param SolicitudCambioSubcontrato $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(SolicitudCambioSubcontrato $model) {
        if ($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudCambioSubcontrato $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(SolicitudCambioSubcontrato $model)
    {
        if ($partidas = $model->partidas) {
            return $this->collection($partidas, new PartidaTransformer);
        }
        return null;
    }

    public function includeRelaciones(SolicitudCambioSubcontrato $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }

    /**
     * Include Empresa
     *
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(SolicitudCambioSubcontrato $model) {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * Include Moneda
     *
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeMoneda(SolicitudCambioSubcontrato $model) {
        if ($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    public function includeComplemento(SolicitudCambioSubcontrato $model)
    {
        if ($item = $model->complemento) {
            return $this->item($item, new SolicitudCambioSubcontratoComplementoTransformer);
        }
        return null;

    }

    public function includeEstadoLabel(SolicitudCambioSubcontrato $model)
    {
        if ($item = $model->estado_label) {
            return $this->item($item, new EstadoLabelTransformer);
        }
        return null;

    }
}
