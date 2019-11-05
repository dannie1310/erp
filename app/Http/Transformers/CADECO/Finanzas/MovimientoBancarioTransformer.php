<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 07:48 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Models\CADECO\Tesoreria\MovimientoBancario;
use League\Fractal\TransformerAbstract;

class MovimientoBancarioTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo',
        'cuenta',
        'transaccion'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo'
    ];

    public function transform(MovimientoBancario $model) {
        return [
            'fecha' => $model->fecha,
            'id' => $model->getKey(),
            'id_cuenta' => $model->id_cuenta,
            'id_tipo_movimiento' => $model->id_tipo_movimiento,
            'importe' => $model->importe,
            'impuesto' => $model->impuesto,
            'numero_folio' => $model->numero_folio,
            'observaciones' => $model->observaciones
        ];
    }

    /**
     * Include TipoMovimiento
     *
     * @param MovimientoBancario $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(MovimientoBancario $model) {
        if ($tipo = $model->tipo) {
            return $this->item($tipo, new TipoMovimientoTransformer);
        }
        return null;
    }

    /**
     * Include Cuenta
     *
     * @param MovimientoBancario $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeCuenta(MovimientoBancario $model) {
        if ($cuenta = $model->cuenta) {
            return $this->item($cuenta, new CuentaTransformer);
        }
        return null;
    }

    /**
     * Include Transaccion
     *
     * @param MovimientoBancario $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTransaccion(MovimientoBancario $model) {
        if ($transaccion = $model->transacciones()->first()) {
            return $this->item($transaccion, new TransaccionTransformer);
        }
        return null;
    }
}