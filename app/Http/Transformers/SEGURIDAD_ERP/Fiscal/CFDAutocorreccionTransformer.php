<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CFDSATTransformer;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;
use League\Fractal\TransformerAbstract;

class CFDAutocorreccionTransformer extends TransformerAbstract
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
        'autocorreccion',
        'cfd',
        'estatus'
    ];

    public function transform(CFDAutocorreccion $model)
    {
        return [
            'id' => $model->getKey(),
            'uuid' => $model->uuid,
        ];
    }

    /**
     * @param CFDAutocorreccion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAutocorreccion(CFDAutocorreccion $model)
    {
        if($autocorreccion = $model->autocorreccion)
        {
            return $this->item($autocorreccion, new AutocorreccionTransformer);
        }
        return null;
    }

    /**
     * @param CFDAutocorreccion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCfd(CFDAutocorreccion $model)
    {
        if($cfd = $model->cfdSat)
        {
            return $this->item($cfd, new CFDSATTransformer);
        }
        return null;
    }

    /**
     * @param CFDAutocorreccion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEstatus(CFDAutocorreccion $model)
    {
        if($estatus = $model->ctgEstado)
        {
            return $this->item($estatus, new CtgEstadosCFDTransformer);
        }
        return null;
    }
}
