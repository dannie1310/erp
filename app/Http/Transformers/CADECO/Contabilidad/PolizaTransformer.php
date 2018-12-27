<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 07:01 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Poliza;
use League\Fractal\TransformerAbstract;

class PolizaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'estatusPrepoliza',
        'transaccionInterfaz',
        'tipoPolizaContpaq'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'estatusPrepoliza',
        'transaccionInterfaz',
        'tipoPolizaContpaq'
    ];

    public function transform(Poliza $model) {
        return [
            'id' => $model->getKey(),
            'concepto' => $model->concepto,
            'fecha' => $model->fecha->format('Y-m-d'),
            'total' => $model->total,
            'cuadre' => $model->cuadre,
            'tiene_historico' => $model->historicos()->count() > 0
        ];
    }

    /**
     * Include EstatusPrepoliza
     *
     * @param Poliza $model
     * @return \League\Fractal\Resource\Item
     */
    public function IncludeEstatusPrepoliza(Poliza $model)
    {
        $estatus = $model->estatusPrepoliza;

        return $this->item($estatus, new EstatusPrepolizaTransformer);
    }

    /**
     * Include TransaccionInterfaz
     *
     * @param Poliza $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTransaccionInterfaz(Poliza $model)
    {
        $transaccionInterfaz = $model->transaccionInterfaz;

        return $this->item($transaccionInterfaz, new TransaccionInterfazTransformer);
    }

    /**
     * Include TipoPolizaContpaq
     *
     * @param Poliza $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipoPolizaContpaq(Poliza $model)
    {
        $tipoPolizaContpaq = $model->tipoPolizaContpaq;

        return $this->item($tipoPolizaContpaq, new TipoPolizaContpaqTransformer);
    }
}