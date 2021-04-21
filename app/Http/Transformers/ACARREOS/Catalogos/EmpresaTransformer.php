<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(Empresa $model) {
        return [
            'id' => (int) $model->getKey(),
            'razon_social' => (string) $model->razonSocial,
            'rfc' => $model->RFC,
            'fecha_registro' => $model->fecha_registro,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'nombre_registro' => (string) $model->nombre_registro,
            'nombre_desactivo' => (string) $model->nombre_desactivo,
            'motivo' => $model->motivo
        ];
    }
}
