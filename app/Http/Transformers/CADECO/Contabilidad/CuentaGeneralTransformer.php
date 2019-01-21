<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:25 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaGeneral;
use League\Fractal\TransformerAbstract;

class CuentaGeneralTransformer extends TransformerAbstract
{
    public function transform(CuentaGeneral $model){
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta_contable
        ];
    }

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo'
    ];

    /**
     * @param CuentaGeneral $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(CuentaGeneral $model){
        if($tipo = $model->tipo){
            return $this->item($tipo, new TipoCuentaGeneralTransformer);
        }
        return null;
    }

}