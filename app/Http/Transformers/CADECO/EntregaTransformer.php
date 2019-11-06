<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/11/2019
 * Time: 01:13 p. m.
 */


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Entrega;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class EntregaTransformer extends TransformerAbstract
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

    public function transform(Entrega $model)
    {
        return [
            'id_item' => $model->getKey(),
            'fecha' => Carbon::parse($model->fecha)->format('d-m-Y'),
            'cantidad'=> $model->cantidad,
            'id_concepto' => $model->id_concepto,
            'id_almacen' => $model->id_almacen
        ];
    }

}
