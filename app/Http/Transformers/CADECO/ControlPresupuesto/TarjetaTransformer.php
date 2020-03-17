<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 13/03/2020
 * Time: 03:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\ControlPresupuesto\Tarjeta;

class TarjetaTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(Tarjeta $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->descripcion,
        ];
    }
}