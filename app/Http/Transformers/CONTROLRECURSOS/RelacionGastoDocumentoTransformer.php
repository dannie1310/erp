<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\RelacionGastoDocumento;
use League\Fractal\TransformerAbstract;

class RelacionGastoDocumentoTransformer extends TransformerAbstract
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
        'tipoDocumento',
        'tipoGasto'
    ];

    public function transform(RelacionGastoDocumento $model){
        return [
            'id' => $model->getKey(),
            'folio' => $model->folio,
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'total_format' => $model->total_format,
            'total' => $model->total,
            'no_personas' => $model->no_personas,
            'importe_format' => $model->importe_format,
            'importe' => $model->importe,
            'iva' => $model->iva,
            'iva_format' => $model->iva_format,
            'retenciones' => $model->retenciones,
            'retenciones_format' => $model->retenciones_format,
            'otros_imp' => $model->otros_impuestos,
            'otros_imp_format' => $model->otros_imp_format,
            'observaciones' => $model->observaciones,
            'uuid' => $model->uuid,
            'estado' => $model->idestado,
            'estado_descripcion' => $model->estatus_descripcion,
            'estado_color' => $model->color_estado,
            'idtipo' => $model->idtipo_docto_comp,
            'idtipogasto' => $model->idtipo_gasto_comprobacion,
            'concepto' => $model->concepto_xml,
            'fecha_editar' => $model->fecha_editar,
            'descuento' => $model->descuento_cfdi,
            'descuento_format' => $model->descuento_format
        ];
    }

    /**
     * @param RelacionGastoDocumento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoDocumento(RelacionGastoDocumento $model)
    {
        if($tipo = $model->tipoDocumento)
        {
            return $this->item($tipo, new TipoDocCompTransformer);
        }
        return null;
    }

    /**
     * @param RelacionGastoDocumento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoGasto(RelacionGastoDocumento $model)
    {
        if($tipo_g = $model->tipoGasto)
        {
            return $this->item($tipo_g, new TipoGastoCompTransformer);
        }
        return null;
    }
}
