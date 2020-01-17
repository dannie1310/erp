<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEstadosEfosTransformer;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use League\Fractal\TransformerAbstract;

class CtgEfosTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'estado'
    ];

    public function transform(CtgEfos $model)
    {
        return[
            'id' => $model->getKey(),
            'rfc' => $model->rfc,
            'razon_social' => $model->razon_social,
            'fecha_presunto' => date("d/m/Y", strtotime($model->fecha_presunto)),
            'fecha_definitivo' =>($model->fecha_definitivo != NULL) ? date("d/m/Y", strtotime($model->fecha_definitivo)) : '--------',
            'estado' => $model->estado,
            'alert_icon' => $model->estado_badge
        ];
    }

    /**
     * @param CtgEfos $model
     * @return \League\Fractal\Resource\Collection|null
     */

    public function includeEstado(CtgEfos $model)
    {
        if ($estado = $model->estadoEfo)
        {
            return $this->item($estado, new CtgEstadosEfosTransformer);
        }
        
        return null;
    }

}
