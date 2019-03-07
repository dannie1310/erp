<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 08:36 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use League\Fractal\TransformerAbstract;

class TipoCuentaContableTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentaContable'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];


    public function transform(TipoCuentaContable $model) {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }

    /**
     * Include CuentaContable
     *
     * @param TipoCuentaContable $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaContable(TipoCuentaContable $model) {
        if($cuenta = $model->cuentaContable) {
            return $this->item($cuenta, new CuentaContableTransformer);
        }
        return null;
    }
}