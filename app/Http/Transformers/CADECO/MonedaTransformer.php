<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2019
 * Time: 10:59 AM
 * User: jfesquivel
 * Date: 19/03/2019
 * Time: 05:42 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Moneda;
use League\Fractal\TransformerAbstract;

class MonedaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'tipo_cambio_cadeco'
    ];

    protected $defaultIncludes = [
        'tipo_cambio_cadeco'
    ];
    /**
     * @param Moneda $model
     * @return array
     */
    public function transform(Moneda $model)
    {

        return [
            'id' => (int)$model->getKey(),
            'nombre'=>(string)$model->nombre,
            'tipo' => $model->tipo,
            'abreviatura'=>(string)$model->abreviatura,
            'tipo_cambio' => $model->tipo_cambio,
            'tipo_cambio_igh' => $model->tipo_cambio_igh
        ];
    }

    /**
     * @param Moneda $moneda
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoCambioCadeco(Moneda $moneda)
    {
        if($cambio = $moneda->cambio)
        {
            return $this->item($cambio, new CambioTransformer);
        } else if($moneda->tipo != 1) {
            abort(400, "No hay ningun tipo de cambio registrado para la moneda: ".$moneda->nombre);
        }
        return null;
    }
}
