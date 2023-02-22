<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 07:04 PM
 */

namespace App\Http\Transformers\CADECO;



use App\Models\CADECO\Almacen;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\Almacenes\MaterialAjustesTransformer;
use App\Http\Transformers\CADECO\Almacenes\MaterialSalidasTransformer;
use App\Http\Transformers\CADECO\Almacenes\TotalesKardexMaterialesTransformer;
use App\Http\Transformers\CADECO\Almacenes\TotalesKardexTransformer;

class AlmacenTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'materiales',
        'materiales_ajuste',
        'materiales_salida',
        'material'
    ];


    public function transform(Almacen $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->descripcion,
            'tipo' => (string) $model->tipo,
            'tipo_almacen' => (int) $model->tipo_almacen,
            'registro' => $model->nombre_registro,
            'fecha_registro' => $model->fecha_registro_format,
            'permiso_editar' => $model->permiso_editar,
            'permiso_eliminar' => $model->permiso_eliminar,
            'numero_economico' => $model->numero_economico,
            'clasificacion' => $model->clasificacion,
            'propiedad' => $model->propiedad,
            'totales' => $model->totales_kardex_materiales
        ];
    }

    /**
     * Include Materiales
     * @param Almacen $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMateriales(Almacen $model){
        if ($materiales = $model->materiales) {
            return $this->collection($materiales, new MaterialTransformer);
        }
        return null;
    }

    /**
     * Include Materiales
     * @param Almacen $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMaterialesAjuste(Almacen $model){
        if ($materiales = $model->materialesAjustables) {
            return $this->collection($materiales, new MaterialAjustesTransformer);
        }
        return null;
    }

    /**
     * Include Materiales
     * @param Almacen $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMaterialesSalida(Almacen $model){
        if ($materiales = $model->materialesSalida) {
            return $this->collection($materiales, new MaterialSalidasTransformer);
        }
        return null;
    }

    /**
     * @param Almacen $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(Almacen $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }
}
