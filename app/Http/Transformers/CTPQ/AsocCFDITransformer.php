<?php


namespace App\Http\Transformers\CTPQ;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CFDSATTransformer;
use App\Models\CTPQ\AsocCFDI;
use League\Fractal\TransformerAbstract;

class AsocCFDITransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'cfdi'
    ];
    protected $defaultIncludes = [
        'cfdi'
    ];
    public function transform(AsocCFDI $model) {
        return [
            'id' => (int) $model->getKey(),
            'uuid' => $model->UUID,
        ];
    }

    public function includeCFDI(AsocCFDI $model)
    {
        if($item = $model->CFDI)
        {
            return $this->item($item, new CFDSATTransformer);
        }
        return null;
    }
}
