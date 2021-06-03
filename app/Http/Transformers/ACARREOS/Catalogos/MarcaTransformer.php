<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Marca;
use League\Fractal\TransformerAbstract;

class MarcaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'historicos'
    ];


    public function transform(Marca $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->Descripcion,
            'usuario_registro' => (string) $model->nombre_registro,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado_format,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
        ];
    }

    /**
     * @param Marca $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Marca $model)
    {
        if($marca = $model->historicos)
        {
            return $this->collection($marca, new MarcaHistoricoTransformer);
        }
        return null;
    }
}
