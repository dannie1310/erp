<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 04:45 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\DocumentoTransformer;
use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use League\Fractal\TransformerAbstract;

class DistribucionRecursoRemesaPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'distribucion_recurso',
        'documento',
        'estado',
        'cuentaAbono',
        'cuentaCargo',
        'moneda',
        'transaccion'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'estado',
        'cuentaAbono',
        'cuentaCargo',
        'moneda'
    ];

    public function transform(DistribucionRecursoRemesaPartida $model){
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha_registro,
            'folio_banco' => $model->folio_partida_bancaria
        ];
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDistribucionRecurso(DistribucionRecursoRemesaPartida $model)
    {
        if($dr = $model->distribucionRecurso){
            return $this->item($dr, new DistribucionRecursoRemesaTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDocumento(DistribucionRecursoRemesaPartida $model)
    {
        if($documento = $model->documento){
            return $this->item($documento, new DocumentoTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEstado(DistribucionRecursoRemesaPartida $model)
    {
        if($estado = $model->estatus){
            return $this->item($estado, new CtgEstadoDistribucionPartidaTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaCargo(DistribucionRecursoRemesaPartida $model)
    {
        if($cuenta = $model->cuentaCargo){
            return $this->item($cuenta, new CuentaTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaAbono(DistribucionRecursoRemesaPartida $model)
    {
        if($cuenta = $model->cuentaAbono){
            return $this->item($cuenta, new CuentaBancariaProveedorTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(DistribucionRecursoRemesaPartida $model)
    {
        if($moneda = $model->moneda){
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTransaccion(DistribucionRecursoRemesaPartida $model)
    {
        if($transaccion = $model->transaccion){
            return $this->item($transaccion, new TransaccionTransformer);
        }
        return null;
    }
}