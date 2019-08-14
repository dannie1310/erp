<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/08/2019
 * Time: 10:40 AM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\FinanzasCBE\SolicitudBaja;
use League\Fractal\TransformerAbstract;

class SolicitudBajaCuentaBancariaTransformer extends TransformerAbstract
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

    public function transform(SolicitudBaja $model)
    {
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta_clabe,
            'sucursal' => $model->sucursal,
            'tipo_cuenta' => $model->tipo,
            'fecha' => $model->fecha,
            'observaciones' => $model->observaciones,
            'estado' => $model->estatus,
            'fecha_format' => $model->fecha_format,
            'estado' => $model->estatus,
            'folio' => $model->numero_folio,
            'numero_folio_format_orden' => $model->numero_folio_format_orden,
            'sucursal_format' => $model->sucursal_format
        ];
    }
}