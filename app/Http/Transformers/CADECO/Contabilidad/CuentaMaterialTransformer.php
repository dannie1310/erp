<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 12:39 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\Contabilidad\CuentaMaterial;
use League\Fractal\TransformerAbstract;

class CuentaMaterialTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'material',
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'material',
        'tipo'
    ];

    public function transform(CuentaMaterial $model){
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => (string) $model->cuenta
        ];
    }

    /**
     * Include Material
     * @return \League\Fractal\Resource\Item
     */
    public function includeMaterial(CuentaMaterial $model){
        $material = $model->material;
        return $this->item($material, new MaterialTransformer);
    }

    /**
     * Include TipoCuentaMaterial
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(CuentaMaterial $model){
        $tipo = $model->tipoCuentaMaterial;
        return $this->item($tipo, new TipoCuentaMaterialTransformer);
    }
}