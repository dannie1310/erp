<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use League\Fractal\TransformerAbstract;

class CtgEfosTransformer extends TransformerAbstract
{

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
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
            'fecha_desvirtuado' =>($model->fecha_desvirtuado != NULL) ? date("d/m/Y", strtotime($model->fecha_desvirtuado)) : '--------',
            'fecha_sentencia_favorable' =>($model->fecha_sentencia_favorable != NULL) ? date("d/m/Y", strtotime($model->fecha_sentencia_favorable)) : '--------',
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
