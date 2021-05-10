<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Material;
use League\Fractal\TransformerAbstract;

class MaterialTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];


    public function transform(Material $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->Descripcion,
            'usuario_registro' => (string) $model->nombre_usuario,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
        ];
    }

}
