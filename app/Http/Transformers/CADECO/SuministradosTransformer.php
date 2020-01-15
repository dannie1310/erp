<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 07/01/2020
 * Time: 01:42 PM
 */

namespace App\Http\Transformers\CADECO;

use App\Models\CADECO\Suministrados;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\MaterialTransformer;

class SuministradosTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'material'
    ];

    public function transform(Suministrados $model)
    {
        return [
            'id_empresa'=> $model->id_empresa,
            'id_material' => $model->id_material,
        ];
    }

    /**
     * @param Suministrados $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(Suministrados $model) {
        if ($material = $model->material) {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }
}