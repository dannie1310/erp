<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\Finanzas\ConfiguracionEstimacionTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\ItemTransformer;
use App\Http\Transformers\CADECO\SubcontratosEstimaciones\SubcontratoEstimacionTrasnformer;
use App\Models\CADECO\Estimacion;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

class EstimacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontratoEstimacion',
        'subcontrato',
        'empresa',
        'moneda',
        'partidas'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Estimacion $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'observaciones' => $model->observaciones,
            'impuesto' => $model->impuesto,
            'impuesto_format' => $model->impuesto_format,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'estado' => $model->estado,
            'fecha' => $model->fecha_format,
            'fecha_inicial'=> $model->cumplimiento_format,
            'fecha_final' => $model->vencimiento_format,
            'subtotal' => $model->subtotal,
            'subtotal_format' => $model->subtotal_format,
            'suma_importes' => $model->suma_importes_format,
            'anticipo' => $model->anticipo,
            'monto_anticipo_aplicado' => $model->monto_anticipo_aplicado,
            'retencion' => $model->subcontrato->retencion,
            'retencion_fondo_garantia' => $model->retencion_fondo_garantia_orden_pago_format,
            'total_retenciones' => $model->retenciones->sum('importe'),
            'retencion_iva' => $model->IVARetenido ? $model->IVARetenido : 0,
            'retencion_iva_format' => $model->IVARetenido ? $model->iva_retenido_format : '$ 0.00',
            'total_retencion_liberadas' => $model->liberaciones->sum('importe'),
            'total_deductivas' => $model->descuentos->sum('importe'),
            'subtotal_orden_pago' => $model->subtotal_orden_pago_format,
            'iva_orden_pago' => $model->iva_orden_pago_format,
            'total_orden_pago' => $model->total_orden_pago_format,
            'total_anticipo_liberar' => $model->anticipo_a_liberar_format,
            'monto_pagar' => $model->monto_a_pagar
        ];
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontratoEstimacion(Estimacion $model)
    {
        if ($subcontratoEstimacion = $model->subcontratoEstimacion)
        {
            return $this->item($subcontratoEstimacion, new SubcontratoEstimacionTrasnformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(Estimacion $model)
    {
        if($subcontrato = $model->subcontrato)
        {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Estimacion $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(Estimacion $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(Estimacion $model)
    {
        if($item = $model->partidas){
            return $this->collection($item, new EstimacionPartidaTransformer);
        }
        return null;
    }
}
