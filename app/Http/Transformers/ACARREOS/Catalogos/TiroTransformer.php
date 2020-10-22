<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Tiro;
use League\Fractal\TransformerAbstract;

class TiroTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(Tiro $model) {
        return [
            'id' => (int) $model->getKey(),
            'clave' => $model->Clave,
            'clave_format' => $model->clave_format,
            'descripcion' => (string) $model->Descripcion,
            'usuario_registro' => (string) $model->nombre_usuario,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'fecha_registro_format' => $model->fecha_registro_completa_format
        ];
    }
}
