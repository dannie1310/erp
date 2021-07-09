<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 01:42 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaMaterialTransformer;
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
        'cuentaMaterial'
    ];

    public function transform(Material $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_material'=> $model->tipo_material,
            'descripcion' => $model->descripcion,
            'tiene_hijos' => $model->tiene_hijos,
            'numero_parte' => $model->numero_parte,
            'unidad' => $model->unidad,
            'tipo_familia' => $model->tipo_familia,
            'tipo_material_descripcion' => $model->tipo_material_descripcion,
            'descripcion_familia' => $model->descripcion_familia,
            'saldo_inventario' => $model->saldo_inventario_format,
            'nivel_padre' => $model->nivel_padre,
            'numero_parte_descripcion' => $model->numero_parte_descripcion
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

    /**
     * Include Cuenta Material
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeCuentaMaterial(Material $model)
    {
        if ($cuenta = $model->cuentaMaterial) {
            return $this->item($cuenta, new CuentaMaterialTransformer);
        }
        return null;
    }
}
