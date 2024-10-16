<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CcDocto;
use League\Fractal\TransformerAbstract;

class CcDoctoTransformer extends TransformerAbstract
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
        'tipoGasto',
        'centroCosto'
    ];

    public function transform(CcDocto $model)
    {
        return [
            'id' => $model->getKey(),
            'total_format' => $model->total_format,
            'importe_format' => $model->importe_format,
            'iva_format' => $model->iva_format,
            'retenciones_format' => $model->retenciones_format,
            'otros_imp_format' => $model->otros_impuestos_format,
            'idtipogasto' => $model->IdTipoGasto,
            'centro_costo' => $model->centro_costo_descripcion,
            'facturable' => $model->facturable_format,
            'id_centro' => $model->IdCC,
            'idfacturable' => $model->Facturable,
            'id_docto' => $model->IdDocto
        ];
    }

    /**
     * @param CcDocto $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoGasto(CcDocto $model)
    {
        if ($tipo = $model->tipoGasto) {
            return $this->item($tipo, new TipoGastoTransformer);
        }
        return null;
    }

    /**
     * @param CcDocto $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCentroCosto(CcDocto $model)
    {
        if($centro =$model->centroCosto)
        {
            return $this->item($centro, new CentroCostoTransformer);
        }
        return null;
    }
}
