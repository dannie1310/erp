<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 07:01 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\Finanzas\TraspasoCuentasTransformer;
use App\Http\Transformers\CADECO\TransaccionTransformer;
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
        'tipoPolizaContpaq',
        'movimientos',
        'transaccionAntecedente',
        'traspaso',
        'relaciones'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'estatusPrepoliza',
        'transaccionInterfaz',
        'tipoPolizaContpaq',
    ];

    public function transform(Poliza $model) {
        return [
            'id' => $model->getKey(),
            'concepto' => $model->concepto,
            'fecha' => $model->fecha_format,
            'fecha_completa' => $model->fecha,
            'numero_folio' => $model->numero_folio_format,
            'total' => $model->total,
            'cuadre' => $model->cuadre,
            'tiene_historico' => $model->historicos()->count() > 0,
            'usuario_solicita' => $model->UsuarioSolicita,
            'poliza_contpaq' => $model->poliza_contpaq,
            'id_poliza' => $model->id_poliza_contpaq,
            'id_empresa' => $model->id_empresa,
            'estatus' => $model->estatus
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

    /**
     * Include PolizaMovimiento
     *
     * @param Poliza $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMovimientos(Poliza $model)
    {
        $movimientos = $model->movimientos;

        return $this->collection($movimientos, new PolizaMovimientoTransformer);
    }

    /**
     * Include Transaccion
     *
     * @param Poliza $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTransaccionAntecedente(Poliza $model) {
        if ($transaccion = $model->transaccionAntecedente) {
            return $this->item($transaccion, new TransaccionTransformer);
        }
        return null;
    }

    /**
     * Include TraspasoCuentas
     *
     * @param Poliza $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTraspaso(Poliza $model) {
        if ($traspaso = $model->traspaso) {
            return $this->item($traspaso, new TraspasoCuentasTransformer);
        }
        return null;
    }

    public function includeRelaciones(Poliza $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }
}
