<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:42 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\AjustePositivo;
use App\Models\CADECO\Almacen;
use League\Fractal\TransformerAbstract;

class AjustePositivoTransformer extends TransformerAbstract
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

    public function transform(AjustePositivo $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_format' => $model->fecha_format,
            'observaciones' => $model->observaciones,
            'estado' => $model->estado,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format_orden,
            'referencia' => $model->referencia,
        ];
    }

    public function includeAlmacen(AjustePositivo $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item(new Almacen);
        }
        return null;
    }
}