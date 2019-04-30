<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 05:44 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaFondoTransformer;
use App\Models\CADECO\Fondo;
use League\Fractal\TransformerAbstract;

class FondoTransformer extends TransformerAbstract
{

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'cuentaFondo'
    ];


    public function transform(Fondo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'saldo' => $model->saldo
        ];
    }

    /**
     * @param CuentaFondo $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeCuentaFondo(Fondo $model){
        if($fondo = $model->cuentaFondo){
            return $this->item($fondo, new CuentaFondoTransformer);
        }
        return null;
    }

}