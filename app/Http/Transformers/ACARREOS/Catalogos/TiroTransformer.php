<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Http\Transformers\CADECO\ConceptoTransformer;
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
        'historicos'
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
            'estado_color' => $model->color_estado,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
            'concepto' => $model->concepto_array,
            'path__corta_concepto' => $model->path_corta_concepto,
            'path_concepto' => $model->path_concepto
        ];
    }

    /**
     * @param Tiro $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Tiro $model)
    {
        if($historicos = $model->historicos)
        {
            return $this->collection($historicos, new TiroHistoricoTransformer);
        }
        return null;
    }
}
