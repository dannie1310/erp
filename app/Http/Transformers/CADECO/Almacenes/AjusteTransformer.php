<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/09/2019
 * Time: 06:19 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Models\CADECO\Ajuste;
use League\Fractal\TransformerAbstract;

class AjusteTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'almacen'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'almacen'
    ];

    public function transform(Ajuste $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_format' => $model->fecha_format,
            'observaciones' => $model->observaciones,
            'estado' => $model->estado,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format_orden,
            'referencia' => $model->referencia,
            'estado_format' => $model->estatus,
            'opciones'=> $model->opciones,
            'tipo' => $model->tipo,
            'tipo_value' => $model->opciones
        ];
    }

    /**
     * @param Ajuste $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAlmacen(Ajuste $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }
}
