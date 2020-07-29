<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 12:12 PM
 */

namespace App\Http\Transformers\CTPQ;


use App\Models\CTPQ\PolizaMovimiento;
use League\Fractal\TransformerAbstract;

class PolizaMovimientoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuenta',
    ];

    protected $defaultIncludes = [
      'cuenta'
    ];

    public function transform(PolizaMovimiento $model) {
        return [
            'id' => (int) $model->getKey(),
            'concepto' => (string) $model->Concepto,
            'referencia' => (string) $model->Referencia,
            'fecha' => (string) $model->Fecha,
            'cargo_format' => (string) $model->cargo_format,
            'abono_format' => (string) $model->abono_format,
            'cuenta' => (string) $model->cuenta->Codigo,
            'tipo' => (int) $model->TipoMovto,
            'importe' => (float) $model->Importe
        ];
    }

    public function includeCuenta(PolizaMovimiento $movimiento)
    {
        if($cuenta = $movimiento->cuenta)
        {
            return $this->item($cuenta, new CuentaTransformer);
        }
        return null;
    }
}
