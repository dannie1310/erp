<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Origen;
use League\Fractal\TransformerAbstract;

class OrigenTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'historicos'
    ];


    public function transform(Origen $model) {
        return [
            'id' => (int) $model->getKey(),
            'clave_format' => $model->clave_format,
            'descripcion' => (string) $model->Descripcion,
            'usuario_registro' => (string) $model->nombre_usuario,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
            'tipo_origen' => $model->tipo_origen_format,
            'id_tipo' => $model->interno,
            'tipo' => $model->tipo_origen_descripcion
        ];
    }

    /**
     * @param Origen $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Origen $model)
    {
        if($historicos = $model->historicos)
        {
            return $this->collection($historicos, new OrigenHistoricoTransformer);
        }
        return null;
    }
}
