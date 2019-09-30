<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Familia;
use League\Fractal\TransformerAbstract;

class FamiliaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'hijos'
    ];

    public function transform(Familia $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_material'=> $model->tipo_material,
            'descripcion' => $model->descripcion,
            'tiene_hijos' => $model->tiene_hijos,
            'numero_parte' => $model->numero_parte,
            'unidad' => $model->unidad,
            'tipo_material_descripcion' => $model->tipo_material_descripcion
        ];
    }

    public function includeHijos(Familia $model)
    {
        if($hijos=$model->hijos){
            return $this->collection($hijos,new MaterialTransformer);
        }
        return null;
    }
}
