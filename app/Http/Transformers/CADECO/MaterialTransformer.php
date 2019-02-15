<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 01:42 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Material;
use League\Fractal\TransformerAbstract;

class MaterialTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'hijos',
    ];

    public function transform(Material $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'tiene_hijos' => $model->tiene_hijos,
            'numero_parte' => $model->numero_parte
        ];
    }

    /**
     * Include Hijos
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeHijos(Material $model)
    {
        if ($hijos = $model->hijos) {
            return $this->collection($hijos, new MaterialTransformer);
        }
        return null;
    }
}