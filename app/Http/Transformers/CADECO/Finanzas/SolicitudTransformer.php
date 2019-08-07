<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 09:07 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use League\Fractal\TransformerAbstract;

class SolicitudTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo',
        'empresa',
        'moneda',
        'banco',
        'plaza',
        'usuario'

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform( $model)
    {
        return [
            'id' => $model->getKey()
        ];
    }
}