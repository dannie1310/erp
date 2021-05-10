<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Material;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\ACARREOS\Catalogos\MaterialHistoricoTransformer;

class MaterialTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'historicos'
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

    /**
     * @param Material $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Material $model)
    {
        if($historicos = $model->historicos)
        {
            return $this->collection($historicos, new MaterialHistoricoTransformer);
        }
        return null;
    }
}
