<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:52 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Cierre;
use League\Fractal\TransformerAbstract;

class CierreTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'apertura'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'apertura'
    ];

    /**
     * @param Cierre $model
     * @return array
     */
    public function transform(Cierre $model) {
        return [
            'id' => (int) $model->getKey(),
            'anio' => (int) $model->anio,
            'mes' => $model->mes($model),
            'fecha' => $model->created_at->format('Y-m-d G:i:s a')
        ];
    }

    public function includeApertura(Cierre $model) {
        if($apertura = $model->apertura){
            return $this->item($apertura, new AperturaTransformer);
        }
        return null;
    }
}