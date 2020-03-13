<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 12/03/2020
 * Time: 08:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;

class SolicitudCambioTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];


    public function transform(SolicitudCambio $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->descripcion,
            'tipo' => (string) $model->tipo,
            'tipo_almacen' => (int) $model->tipo_almacen
        ];
    }

    // /**
    //  * Include Materiales
    //  * @param Almacen $model
    //  * @return \League\Fractal\Resource\Collection
    //  */
    // public function includeMateriales(Almacen $model){
    //     if ($materiales = $model->materiales) {
    //         return $this->collection($materiales, new MaterialTransformer);
    //     }
    //     return null;
    // }
}