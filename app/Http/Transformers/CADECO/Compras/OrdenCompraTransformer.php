<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\Compras\OrdenCompraPartidaTransformer;
use App\Models\CADECO\OrdenCompra;
use League\Fractal\TransformerAbstract;

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
        'partidas'
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
            'subtotal' => (float)$model->subtotal,
            'subtotal_format' => (string) '$ '.number_format(($model->subtotal),2,".",","),
            'impuesto' => (float)$model->impuesto,
            'impuesto_format' => (string) '$ '.number_format($model->impuesto,2,".",","),
            'monto' => (float)$model->monto,
            'total_format' => (string)$model->monto_format,
            'monto_format' => (string)$model->monto_format,
            'referencia' => (string)$model->referencia,
            'retencion' => (float)$model->retencion,
            'anticipo' => (float)$model->anticipo,
            'observaciones' => (string)$model->observaciones,
            'observaciones_format' => (string)$model->observaciones_format,
            'id_moneda' => (int)$model->id_moneda,
            'destino '=> (string)$model->destino,
            'saldo' => (float)$model->saldo,
            'tipo_nombre' => (string)$model->getNombre(),
            'dato_transaccion' => (string)$model->getEncabezadoReferencia(),
            'monto_facturado_oc' => (float) $model->montoFacturadoOrdenCompra,
            'monto_facturado_ea' => (float) $model->montoFacturadoEntradaAlmacen,
            'monto_solicitado' => (float) $model->montoPagoAnticipado,
            'entradas_almacen' =>  $model->tiene_entrada_almacen,
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

}
