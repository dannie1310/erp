<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 06:09 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use League\Fractal\TransformerAbstract;

class SolicitudMovimientoFondoGarantiaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'fondo_garantia',
        'tipo_solicitud',
        'movimientos'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'fondo_garantia',
        'tipo_solicitud'
    ];

    /**
     * @param SolicitudMovimientoFondoGarantia $model
     * @return array
     */
    public function transform(SolicitudMovimientoFondoGarantia $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'numero_folio_format' => (string)$model->numero_folio_format,
            'fecha_format' => (string)$model->fecha_format,
            'referencia' => (string)$model->referencia,
            'importe' => (string)$model->importe_format,
            'observaciones' => (string)$model->observaciones,
            'usuario_registra'=>(string)$model->usuario_registra,
            'usuario_registra_desc'=>(string)$model->usuario_registra_desc,
            'estado_desc'=>(string)$model->estado_desc,
            'estado'=>(string)$model->estado,
            'created_at'=>(string)$model->created_at,
        ];
    }

    /**
     * Include Fondo de Garantia
     *
     * @param SolicitudMovimientoFondoGarantia $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeFondoGarantia(SolicitudMovimientoFondoGarantia $model) {
        if ($fondo_garantia = $model->fondo_garantia) {
            return $this->item($fondo_garantia, new FondoGarantiaTransformer);
        }
        return null;
    }

    /**
     * Include Tipo de Solicitud
     *
     * @param SolicitudMovimientoFondoGarantia $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipoSolicitud(SolicitudMovimientoFondoGarantia $model) {
        if ($tipo_solicitud = $model->tipo) {
            return $this->item($tipo_solicitud, new CtgTipoSolicitudTransformer);
        }
        return null;
    }

    /**
     * Include Movimientos
     *
     * @param SolicitudMovimientoFondoGarantia $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMovimientos(SolicitudMovimientoFondoGarantia $model) {
        if ($movimientos = $model->movimientos) {
            return $this->collection($movimientos, new MovimientoSolicitudMovimientoFondoGarantiaTransformer);
        }
        return null;
    }
}
