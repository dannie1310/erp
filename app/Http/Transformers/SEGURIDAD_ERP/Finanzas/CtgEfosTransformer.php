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
            'fecha_presunto' => $model->fecha_presunto_format,
            'fecha_definitivo' =>$model->fecha_definitivo_format,
            'fecha_desvirtuado' =>$model->fecha_desvirtuado_format,
            'fecha_sentencia_favorable' =>$model->fecha_sentencia_favorable_format,
            'fecha_presunto_dof' => $model->fecha_presunto_dof_format,
            'fecha_definitivo_dof' =>$model->fecha_definitivo_dof_format,
            'fecha_desvirtuado_dof' =>$model->fecha_desvirtuado_dof_format,
            'fecha_sentencia_favorable_dof' =>$model->fecha_sentencia_favorable_dof_format,
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
