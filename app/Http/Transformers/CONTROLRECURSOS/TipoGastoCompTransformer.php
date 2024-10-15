<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TipoGastoComp;
use League\Fractal\TransformerAbstract;

class TipoGastoCompTransformer extends TransformerAbstract
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
        'tipoGasto'
    ];

    public function transform(TipoGastoComp $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->Descripcion
        ];
    }

    /**
     * @param TipoGastoComp $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoGasto(TipoGastoComp $model)
    {
        if($tipo = $model->tipoGasto)
        {
            return $this->item($tipo, new TipoGastoTransformer);
        }
        return null;

    }
}
