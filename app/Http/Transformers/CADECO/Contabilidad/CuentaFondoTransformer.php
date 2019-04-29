<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 05:09 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\CADECO\FondoTransformer;
use App\Models\CADECO\Contabilidad\CuentaFondo;
use League\Fractal\TransformerAbstract;

class CuentaFondoTransformer extends TransformerAbstract
{

    public function transform(CuentaFondo $model){

        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta
        ];
    }

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'fondo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    /**
     * @param CuentaFondo $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeFondo(CuentaFondo $model){
        if($fondo = $model->fondo){
            return $this->item($fondo, new FondoTransformer);
        }
        return null;
    }
}