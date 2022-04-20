<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Factura;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\CambioTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\ContraReciboTransformer;
use App\Http\Transformers\CADECO\Contabilidad\PolizaTransformer;
use App\Http\Transformers\CADECO\Finanzas\ItemFacturaTransformer;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\DocumentoTransformer;

class FacturaTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'contra_recibo',
       'documento',
        'empresa',
        'moneda',
        'complemento',
        'cambio',
        'poliza',
        'relaciones',
        'partidas'
    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Factura $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_cr' => $model->contra_recibo->fecha_format,
            'numero_folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'antecedente' => $model->id_antecedente,
            'subtotal'=>(float)$model->subtotal,
            'subtotal_format'=>(string)$model->subtotal_format,
            'impuesto'=>(float)$model->impuesto,
            'impuesto_format'=>(string) $model->impuesto_format,
            'monto'=>(float)$model->monto,
            'saldo_format'=>$model->saldo_format,
            'total_format'=>(string)$model->monto_format,
            'monto_format'=>(string)$model->monto_format,
            'referencia'=>(string)$model->referencia,
            'retencion'=>(float)$model->retencion,
            'anticipo'=>(float)$model->anticipo,
            'observaciones'=>(string)$model->observaciones,
            'observaciones_format'=>(string)$model->observaciones_format,
            'tipo_solicitud'=>(int) $model->tipo_transaccion,
            'fecha' => (string)$model->fecha,
            'fecha_format' => (string)$model->fecha_format,
            'estado_format'=>$model->estado_string,
            'estado' => (int)$model->estado,
            'opciones_format'=>$model->tipo_transaccion_string,
            'cumplimiento' => (string)$model->cumplimiento_form,
            'vencimiento' => $model->vencimiento_form,
            'vencimiento_format' => $model->vencimiento_format,
            'tipo_cambio' => $model->tipo_cambio,
            'tipo_cambio_fecha' => $model->tipoCambioFechaRevision(),
            'a_pagar' => $model->autorizado,
            'a_cuenta' => $model->a_cuenta_format,
            'moneda' => $model->moneda->nombre,
            'id_moneda' => $model->id_moneda,
            'id_costo' => $model->id_costo,
            'empresa' => $model->empresa->razon_social,
            'id_rubro' => (int) $model->id_rubro,
            'rubro' => $model->rubro,
            'datos_registro' => $model->datos_registro,
            'comentario' => $model->comentario,
            'fondo_garantia_format' => $model->fondo_garantia_format,
            'retenciones_format' => $model->retenciones_subcontrato_format,
            'devoluciones_format' => $model->devoluciones_subcontrato_format,
            'tipo' => $model->tipo->Descripcion,
            'opciones' => $model->opciones,
            'costo_tipo_gasto' => $model->por_tipo_gasto,
            'impuesto_retenido' => $model->impuesto_retenido,
            'impuesto_retenido_format' => $model->impuesto_retenido_format,
        ];
    }

    public function includeContraRecibo(Factura $model)
    {

        if($contrarecibo = $model->contra_recibo){
            return $this->item($contrarecibo, new ContraReciboTransformer);
        }
        return null;

    }

    public function includeDocumento(Factura $model)
    {
        if ($documento = $model->documento) {
            return $this->item($documento, new DocumentoTransformer);
        }
        return null;
    }

    public function includeMoneda(Factura $model)
    {
        if ($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    public function includePoliza(Factura $model)
    {
        if ($poliza = $model->prepolizaActiva()) {
            return $this->item($poliza, new PolizaTransformer);
        }
        return null;
    }

    public function includeEmpresa(Factura $model){
        if($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    public function includeComplemento(Factura $model){
        if($complemento = $model->complemento) {
            return $this->item($complemento, new ComplementoFacturaTransformer);
        }
        return null;
    }

    public function includeCambio(Factura $model){
        if($cambio = $model->tipoCambioFecha) {
            return $this->collection($cambio, new CambioTransformer);
        }
        return null;
    }

    public function includeRelaciones(Factura $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }

    public function includePartidas(Factura $model){
        if($partidas = $model->partidas){
            return $this->collection($partidas, new ItemFacturaTransformer);
        }
        return null;
    }
}
