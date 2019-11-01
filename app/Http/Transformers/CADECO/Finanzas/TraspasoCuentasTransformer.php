<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:29 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use League\Fractal\TransformerAbstract;

class TraspasoCuentasTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentaOrigen',
        'cuentaDestino',
        'traspasoTransaccion'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'cuentaOrigen',
        'cuentaDestino',
        'traspasoTransaccion'
    ];

    public function transform(TraspasoCuentas $model) {
        $array = $model->toArray();
        $array['importe'] = round($array['importe'], 2);
        return $array;
    }

    public function includeCuentaOrigen(TraspasoCuentas $model)
    {
        $cuenta_origen = $model->cuentaOrigen;
        return $this->item($cuenta_origen, new CuentaTransformer);
    }

    public function includeCuentaDestino(TraspasoCuentas $model)
    {
        $cuenta_destino = $model->cuentaDestino;
        return $this->item($cuenta_destino, new CuentaTransformer);
    }

    public function includeTraspasoTransaccion(TraspasoCuentas $model)
    {
        $transaccion = $model->traspasoTransaccion;
        return $this->item($transaccion, new TraspasoTransaccionTransformer);
    }
}