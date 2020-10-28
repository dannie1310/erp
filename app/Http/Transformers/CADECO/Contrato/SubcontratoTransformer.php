<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/02/2019
 * Time: 11:07 AM
 */

namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\Subcontrato\SubcontratosTransformer;
use App\Models\CADECO\Subcontrato;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\Auxiliares\RelacionTransformer;

class SubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'moneda',
        'estimaciones',
        'partidas',
        'partidas_ordenadas',
        'subcontratos',
        'relaciones'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * @param Subcontrato $model
     * @return array
     */
    public function transform(Subcontrato $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha_format' => (string)$model->fecha_format,
            'fecha' => (string)$model->fecha,
            'numero_folio_format'=>(string)$model->numero_folio_format,
            'contrato_folio_format' => (string)$model->contratoProyectado->numero_folio_format,
            'subtotal'=>(float)$model->subtotal,
            'subtotal_format'=>(string) '$ '.number_format(($model->subtotal),2,".",","),
            'subtotal_antes_descuento' =>(string) '$ '.number_format(($model->subtotal_antes_descuento),2,".",","),
            'descuento' =>(string)number_format($model->PorcentajeDescuento, 2, ".", ","),
            'impuesto'=>(float)$model->impuesto,
            'impuesto_format'=>(string) '$ '.number_format($model->impuesto,2,".",","),
            'impuesto_retenido' =>(string) '$ '.number_format($model->impuesto_retenido,2,".",","),
            'retencion_iva' => $model->impuesto_retenido,
            'monto'=>(float)$model->monto,
            'costo'=>(string)($model->costo) ? $model->costo->descripcion : '-----',
            'tipo_subcontrato'=>(string)($model->clasificacionSubcontrato) ? $model->clasificacionSubcontrato->tipo->descripcion : '------',
            'personalidad_contratista'=>(string)$model->empresa->personalidad_definicion,
            'estado' => (int)$model->estado,
            'monto_format'=>(string)$model->monto_format,
            'referencia'=>(string)$model->referencia,
            'retencion'=>(string)$model->retencion.' %',
            'retencion_fg'=>$model->retencion,
            'anticipo'=>(float)$model->anticipo,
            'anticipo_format' => $model->anticipo_format,
            'anticipo_monto_format'=>(string) '$ '.number_format($model->anticipo_monto, 2, ".", ","),
            'observaciones'=>(string)$model->observaciones,
            'id_moneda' =>(int)$model->id_moneda,
            'destino' =>(string)$model->destino,
            'saldo'=>(float)$model->saldo,
            'tipo_nombre'=>(string)$model->getNombre(),
            'dato_transaccion'=>(string)$model->referencia,
            'monto_solicitado' => (float) $model->montoPagoAnticipado,
            'monto_facturado_oc' => (float) $model->montoFacturadoSubcontrato,
            'monto_facturado_ea' => (float) $model->montoFacturadoEstimacion,
            'empresa'=>(string) $model->empresa->razon_social
        ];
    }
    /**
     * Include Empresa
     *
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(Subcontrato $model) {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * Include Moneda
     *
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeMoneda(Subcontrato $model) {
        if ($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * Include Subcontratos
     *
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeSubcontratos(Subcontrato $model)
    {
        if ($subcontrato = $model->subcontratos)
        {
            return $this->item($subcontrato, new SubcontratosTransformer);
        }
        return [
            'id' => null,
            'descripcion' => '',
            'fecha_ini' => ($model->fecha_ini_ejec) ? date("d/m/Y", strtotime($model->fecha_ini_ejec)) : '--------',
            'fecha_fin' => ($model->fecha_fin_ejec) ? date("d/m/Y", strtotime($model->fecha_fin_ejec)) : '--------'
        ];
    }

    /**
     * Include Estimaciones
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeEstimaciones(Subcontrato $model)
    {
        if($estimaciones = $model->estimaciones){
            return $this->collection($estimaciones, new EstimacionTransformer);
        }
        return null;
    }

    public function includePartidas(Subcontrato $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new SubcontratoPartidaTransformer);
        }
        return null;
    }

    public function includePartidasOrdenadas(Subcontrato $model)
    {
        if($partidas = $model->partidasOrdenadas)
        {
            return $this->collection($partidas, new SubcontratoPartidaTransformer);
        }
        return null;
    }
    public function includeRelaciones(Subcontrato $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }
}
