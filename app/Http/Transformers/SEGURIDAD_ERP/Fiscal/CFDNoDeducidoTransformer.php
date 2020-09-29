<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CFDSATTransformer;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDNoDeducido;
use League\Fractal\TransformerAbstract;

class CFDNoDeducidoTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'estatus'
    ];

    protected $availableIncludes = [
        'noDeducido',
        'cfd',
        'estatus'
    ];

    public function transform(CFDNoDeducido $model)
    {
        return [
            'id' => $model->getKey(),
            'uuid' => $model->uuid,
        ];
    }

    /**
     * @param CFDNoDeducido $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeNoDeducido(CFDNoDeducido $model)
    {
        if($noDeducido = $model->noDeducido)
        {
            return $this->item($noDeducido, new NoDeducidoTransformer);
        }
        return null;
    }

    /**
     * @param CFDNoDeducido $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCfd(CFDNoDeducido $model)
    {
        if($cfd = $model->cfdSat)
        {
            return $this->item($cfd, new CFDSATTransformer);
        }
        return null;
    }

    /**
     * @param CFDNoDeducido $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEstatus(CFDNoDeducido $model)
    {
        if($estatus = $model->ctgEstado)
        {
            return $this->item($estatus, new CtgEstadosCFDTransformer);
        }
        return null;
    }
}
