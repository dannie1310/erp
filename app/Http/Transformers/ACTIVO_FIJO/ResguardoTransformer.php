<?php


namespace App\Http\Transformers\ACTIVO_FIJO;


use League\Fractal\TransformerAbstract;

class ResguardoTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model->IdResguardo,
            'idEmpleado' => $model->IdEmpleado,
            'nombreEmpleado' => $model->nombre_usuario_info,
            'idUbicacion' => $model->IdProyecto,
            'ubicacion' => $model->ubicacion->ubicacion,
            'grupoEquipo' => $model->GrupoEquipo,
            'grupoEquipoNombre' => $model->grupoActivo ? $model->grupoActivo->descripcion : '',
            'fecha_creo' => $model->FechaCreo
        ];
    }
}
