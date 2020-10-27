<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Models\CADECO\OrdenCompra;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\SucursalTransformer;
use App\Http\Transformers\CADECO\Compras\OrdenCompraPartidaTransformer;
use App\Http\Transformers\CADECO\Compras\OrdenCompraComplementoTransformer;

class OrdenCompraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'solicitud',
        'partidas',
        'sucursal',
        'usuario',
        'moneda',
        'complemento',
        'relaciones'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(OrdenCompra $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha_format' => (string)$model->fecha_format,
            'numero_folio_format' => (string)$model->numero_folio_format,
            'folio_asignacion' => (string)$model->numero_folio_asignacion_format,
            'folio_cotizacion' => (string)$model->numero_folio_cotizacion_format,
            'subtotal' => (float)$model->subtotal,
            'subtotal_format' => (string) '$ '.number_format(($model->subtotal),2,".",","),
            'impuesto' => (float)$model->impuesto,
            'impuesto_format' => (string) '$ '.number_format($model->impuesto,2,".",","),
            'monto' => (float)$model->monto,
            'total_format' => (string)$model->monto_format,
            'monto_format' => (string)$model->monto_format,
            'referencia' => (string)$model->referencia,
            'retencion' => (float)$model->retencion,
            'anticipo' => (float)$model->anticipo_orden,
            'observaciones' => (string)$model->observaciones,
            'observaciones_format' => (string)$model->observaciones_format,
            'id_moneda' => (int)$model->id_moneda,
            'id_costo' => (int)$model->id_costo,
            'destino '=> (string)$model->destino,
            'saldo' => (float)$model->saldo,
            'tipo_nombre' => (string)$model->nombre,
            'dato_transaccion' => (string)$model->encabezado_referencia,
            'monto_facturado_oc' => (float) $model->monto_facturado_orden_compra,
            'monto_facturado_ea' => (float) $model->monto_facturado_entrada_almacen,
            'monto_solicitado' => (float) $model->monto_pago_anticipado,
            'entradas_almacen' =>  $model->tiene_entrada_almacen,
            'porcentaje_anticipo_pactado' => $model->porcentaje_anticipo_pactado,
        ];
    }

    /**
     * Include Empresa
     *
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(OrdenCompra $model)
    {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * Include Sucursal
     *
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeSucursal(OrdenCompra $model)
    {
        if($sucursal = $model->sucursal)
        {
            return $this->item($sucursal, new SucursalTransformer);
        }
        return null;
    }

    /**
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitud(OrdenCompra $model)
    {
        if ($solicitud = $model->solicitud) {
            return $this->item($solicitud, new SolicitudCompraTransformer);
        }
        return null;
    }

    /**
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(OrdenCompra $model)
    {
        if($partidas = $model->partidas){
            return $this->collection($partidas, new OrdenCompraPartidaTransformer);
        }
        return null;
    }

    /**
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeUsuario(OrdenCompra $model)
    {
        if ($usuario = $model->usuario) {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(OrdenCompra $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeComplemento(OrdenCompra $model){
        if($complemento = $model->complemento){
            // dd($complemento);
            return $this->item($complemento, new OrdenCompraComplementoTransformer);
        }
        return null;
    }

    // public function includeComplemento(OrdenCompra $model){
    //     if($complemento = $model->complemento){
    //         return $this->item($complemento, new );
    //     }
    //     return null;
    // }
    public function includeRelaciones(OrdenCompra $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }
}
