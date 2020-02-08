<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 04:24 PM
 */

namespace App\Models\CADECO;
use App\Facades\Context;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Estimaciones\EstimacionEliminada;
use App\Models\CADECO\Estimaciones\EstimacionPartidaEliminada;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Models\CADECO\SubcontratosEstimaciones\FolioPorSubcontrato;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;
use App\Models\CADECO\SubcontratosFG\RetencionFondoGarantia;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Moneda;
use Illuminate\Support\Facades\DB;

class Estimacion extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;
    public const OPCION_ANTECEDENTE = 2;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        'id_usuario'
    ];

    public $searchable = [
        'numero_folio',
        'observaciones',
        'subcontrato.empresa.razon_social',
        'subcontrato.referencia'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 52)
                ->where(function ($q3) {
                    return $q3
                        ->whereHas('subcontrato');
                });
        });
    }

    /**
     * Relaciones Eloquent
     */
    public function retencion_fondo_garantia()
    {
        return $this->hasOne(RetencionFondoGarantia::class, 'id_estimacion', 'id_transaccion');
    }

    public function subcontrato()
    {
        return $this->belongsTo(Subcontrato::class, 'id_antecedente', 'id_transaccion');
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'id_transaccion', 'id_transaccion');
    }

    public function liberaciones()
    {
        return $this->hasMany(Liberacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function subcontratoEstimacion()
    {
        return $this->hasOne(\App\Models\CADECO\SubcontratosEstimaciones\Estimacion::class, 'IDEstimacion', 'id_transaccion');
    }

    public function retenciones()
    {
        return $this->hasMany(Retencion::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Acciones
     */
    public function creaSubcontratoEstimacion()
    {
        \App\Models\CADECO\SubcontratosEstimaciones\Estimacion::query()->create([
            'IDEstimacion' => $this->id_transaccion,
            'NumeroFolioConsecutivo' => $this->generaFolioConsecutivo()
        ]);
    }

    public static function calcularFolio()
    {
        $est = Transaccion::query()->where('tipo_transaccion', '=', 52)->orderBy('numero_folio', 'DESC')->first();
        return $est ? $est->numero_folio + 1 : 1;
    }

    private function generaFolioConsecutivo()
    {
        $folio = FolioPorSubcontrato::query()
            ->where('IDSubcontrato', '=', $this->id_antecedente)
            ->first();

        if ($folio) {
            $folio->UltimoFolio += 1;
            $folio->save();
        } else {
            $folio = FolioPorSubcontrato::query()->create([
                'IDSubcontrato' => $this->id_antecedente,
                'UltimoFolio' => 1
            ]);
        }
        return $folio->UltimoFolio;
    }

    public function calculaImportes()
    {
        // Calculo del importe de amortizacion de anticipo
        $amortizacion_anticipo = ($this->subcontrato->anticipo / 100) * $this->sumaImportes;

        // Calculo del importe de fondo de garantia
        $fondo_garantia = ($this->subcontrato->retencion / 100) * $this->sumaImportes;

        // Calculo del subtotal
        $subtotal = $this->sumaImportes;

        // Descuento de amortizacion de anticipo antes de iva
        $subtotal -= $amortizacion_anticipo;

        // Se calcula el iva y total
        $iva = $subtotal * ($this->subcontrato->impuesto / ($this->subcontrato->monto - $this->subcontrato->impuesto));
        $total = $subtotal + $iva;

        $this->impuesto = $iva;
        $this->monto = $total;
        $this->saldo = $total;
        $this->retencion = ($this->subcontrato->retencion / 100) * 100;
        $this->anticipo = ($this->subcontrato->anticipo / 100);
        $this->save();

        $subcontratoEstimacion = \App\Models\CADECO\SubcontratosEstimaciones\Estimacion::query()
            ->where('IDEstimacion', '=', $this->id_transaccion)
            ->first();

        $subcontratoEstimacion->PorcentajeFondoGarantia = ($this->subcontrato->retencion / 100);
        $subcontratoEstimacion->ImporteFondoGarantia = $fondo_garantia;
        $subcontratoEstimacion->save();
    }

    /**
     * Genera la Retención de Fondo de Garantía.
     * @throws \Exception
     */
    public function generaRetencion()
    {
        if ($this->retencion > 0) {
            if (is_null($this->retencion_fondo_garantia)) {
                $this->retencion_fondo_garantia()->create(
                    [
                        'id_estimacion' => $this->id_transaccion,
                        'importe' => $this->importeRetencionFondoGarantia()
                    ]
                );
            } else {
                $this->retencion_fondo_garantia()->update(
                    [
                        'importe' => $this->importeRetencionFondoGarantia()
                    ]
                );
                $this->retencion_fondo_garantia->generaMovimientoRegistro();
            }
        }else{
            throw New \Exception('La estimación no tiene establecido un porcentaje de retención de fondo de garantía, la retención no puede generarse');
        }
    }

    public function aprobar()
    {
        if ($this->sumaImportes == 0) {
            throw new \Exception('La estimacion no tiene importe');
        }
        if ($this->estado > 0) {
            throw new \Exception('La estimacion se encuentra aprobada.');
        }

        $fecha = date("d/m/Y H:i");
        $usuario = auth()->user()->usuario;
        $this->comentario = $this->comentario . "A;{$fecha};{$usuario}|";
        $this->impreso = 1;
        $this->saldo = $this->monto;
        $this->save();

        DB::connection('cadeco')->update("EXEC [dbo].[sp_aprobar_transaccion] {$this->id_transaccion}");

        return $this;
    }

    public function revertirAprobacion()
    {
        if ($this->estado == 2) {
            throw new \Exception('La transacción no puede modificarse por que esta aprobada o revisada.');
        }

        DB::connection('cadeco')->update("EXEC [dbo].[sp_revertir_transaccion] {$this->id_transaccion}");

        return $this;
    }

    public function getImporteRetencionAttribute()
    {
        return $this->suma_importes * $this->retencion / 100;
    }

    public function getSumaImportesAttribute()
    {
        return $this->items->sum('importe');
    }

    public function getSumaImportesFormatAttribute()
    {
        return '$ ' . number_format($this->suma_importes, 2,".",",");
    }

    public function items()
    {
        return $this->hasMany(ItemEstimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function movimientos()
    {
        return $this->hasManyThrough(Movimiento::class, ItemEstimacion::class, "id_transaccion", "id_item", "id_transaccion", "id_item");
    }

    public function getAmortizacionPendienteAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();
        return $this->subcontrato->anticipo_monto - $estimaciones_anteriores->sum('monto_anticipo_aplicado') - $this->monto_anticipo_aplicado;
    }

    public function getAmortizacionPendienteAnteriorAttribute()
    {
        $estimacion_anterior = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->orderBy('id_transaccion', 'DESC')->first();
        if ($estimacion_anterior) {
            return $estimacion_anterior->amortizacion_pendiente;
        } else {
            return 0;
        }
    }

    public function getMontoAnticipoAplicadoAttribute()
    {
        return $this->suma_importes * ($this->anticipo / 100);
    }

    public function getRetenidoAnteriorAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();

        $sumatoria = 0;
        foreach ($estimaciones_anteriores as $estimacion) {
            $sumatoria += $estimacion->SumMontoRetencion;
        }
        return $sumatoria;
    }

    public function getRetenidoOrigenAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();

        $sumatoria = 0;
        foreach ($estimaciones_anteriores as $estimacion) {
            $sumatoria += $estimacion->SumMontoRetencion;
        }
        return $sumatoria + $this->SumMontoRetencion;
    }

    public function getConfiguracionAttribute()
    {
        $configuracion = $this->obra->configuracionEstimaciones;
        if(!$configuracion)
        {
            $configuracion=ConfiguracionEstimacion::create([
                'penalizacion_antes_iva'=>1,
                'retenciones_antes_iva'=>1,
                'ret_fon_gar_antes_iva'=>1,
                'desc_pres_mat_antes_iva'=>1,
                'desc_otros_prest_antes_iva'=>0,
                'ret_fon_gar_con_iva'=>0,
            ]);

        }
        return $configuracion;
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function partidas()
    {
        return $this->hasMany(EstimacionPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'id_antecedente', 'id_transaccion');
    }

    public function prepoliza()
    {
        return $this->belongsTo(Poliza::class, 'id_transaccion', 'id_transaccion_sao');
    }

    public function estimacionEliminada()
    {
        return $this->belongsTo(EstimacionEliminada::class, 'id_transaccion');
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->respaldar($motivo);
            $this->partidas()->delete();
            $this->delete();
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    public function validarParaEliminar()
    {
        $mensaje = "";

        if ($this->estado != 0) {
            abort(400, "No se puede eliminar está estimación porque se encuentra Autorizada.");
        }

        if ($this->prepoliza()->first() != null) {
            if ($this->prepoliza->estatus != -3) {
                $mensaje = $mensaje . "-Prepoliza: # " . $this->prepoliza->id_int_poliza . " \n";
            };
        }

        if ($this->facturas()->first() != null) {
            $factura_item = [];
            foreach ($this->facturas as $factura) {
                array_push($mensaje_items, "-Factura: #" . $factura->numero_folio . " \n");
            }

            $factura_item = array_unique($factura_item);
            if ($factura_item != []) {
                $mensaje_fin = "";
                foreach ($factura_item as $mensaje_item) {
                    $mensaje_fin = $mensaje_fin . $mensaje_item;
                }
                $mensaje = $mensaje . $mensaje_fin;
            }
        }

        if ($mensaje != "") {
            abort(400, "No se puede eliminar la estimación debido a que existen las siguientes transacciones relacionadas:\n" . $mensaje . "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }
    }

    public function respaldar($motivo)
    {
        /**
         * Respaldar partidas
         */
        foreach ($this->partidas as $partida) {
            EstimacionPartidaEliminada::create([
                'id_item' => $partida->id_item,
                'id_transaccion' => $partida->id_transaccion,
                'id_antecedente' => $partida->id_antecedente,
                'item_antecedente' => $partida->item_antecedente,
                'id_concepto' => $partida->id_concepto,
                'cantidad' => $partida->cantidad,
                'importe' => $partida->importe,
                'precio_unitario' => $partida->precio_unitario,
                'estado' => $partida->estado
            ]);
        }

        /**
         * Respaldar estimación
         */
        EstimacionEliminada::create([
            'id_transaccion' => $this->id_transaccion,
            'id_antecedente' => $this->id_antecedente,
            'tipo_transaccion' => $this->tipo_transaccion,
            'numero_folio' => $this->numero_folio,
            'fecha' => $this->fecha,
            'estado' => $this->estado,
            'impreso' => $this->impreso,
            'id_obra' => $this->id_obra,
            'id_empresa' => $this->id_empresa,
            'id_moneda' => $this->id_moneda,
            'cumplimiento' => $this->cumplimiento,
            'vencimiento' => $this->vencimiento,
            'opciones' => $this->opciones,
            'monto' => $this->monto,
            'saldo' => $this->saldo,
            'autorizado' => $this->autorizado,
            'impuesto' => $this->impuesto,
            'anticipo' => $this->anticipo,
            'tipo_cambio' => $this->anticipo,
            'comentario' => $this->comentario,
            'observaciones' => $this->observaciones ? $this->observaciones : '',
            'FechaHoraRegistro' => $this->FechaHoraRegistro,
            'IVARetenido' => $this->IVARetenido,
            'id_usuario' => $this->id_usuario,
            'motivo_eliminacion' => $motivo,
        ]);
    }

    public function getSubtotalAttribute()
    {
        return $this->monto - $this->impuesto;
    }

    public function getSubtotalFormatAttribute()
    {
        return '$ ' . number_format($this->subtotal, 2);
    }

    public function getSubtotalOrdenPagoAttribute()
    {
        $subtotal = $this->suma_importes- $this->monto_anticipo_aplicado;
        if($this->configuracion->retenciones_antes_iva == 1){
            $subtotal-=$this->retenciones->sum("importe");
            $subtotal-=$this->IVARetenido;
            $subtotal+=$this->liberaciones->sum("importe");
        }
        if($this->configuracion->desc_pres_mat_antes_iva == 1){
            $subtotal-=$this->descuentos->sum("importe");
        }
        if($this->configuracion->ret_fon_gar_antes_iva == 1){
            $subtotal-=$this->retencion_fondo_garantia_orden_pago;
        }
        return $subtotal;
    }

    public function getSubtotalOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->subtotal_orden_pago, 2);
    }

    public function getIvaOrdenPagoAttribute()
    {
        return $this->subtotal_orden_pago * 0.16;
    }

    public function getIvaOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->iva_orden_pago, 2);
    }

    public function getTotalOrdenPagoAttribute()
    {
        $total = $this->subtotal_orden_pago + $this->iva_orden_pago;
        return $total;
    }
    # retencion_fondo_garantia_orden_pago_format

    public function getRetencionFondoGarantiaOrdenPagoAttribute()
    {
        if($this->configuracion->ret_fon_gar_antes_iva == 0 ){
            if($this->configuracion->ret_fon_gar_con_iva == 1){
                return $this->suma_importes * ($this->retencion/100) * 1.16;
            } else{
                return $this->suma_importes * ($this->retencion/100) ;
            }
        } else {
            return $this->suma_importes * ($this->retencion/100);
        }
    }

    public function getRetencionFondoGarantiaOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->retencion_fondo_garantia_orden_pago, 2);
    }

    public function getAnticipoALiberarAttribute()
    {
        return $this->subcontratoEstimacion ? $this->subcontratoEstimacion->ImporteAnticipoLiberar:0;
    }

    public function getAnticipoALiberarFormatAttribute()
    {
        return '$ ' . number_format($this->anticipo_a_liberar, 2);
    }

    public function getTotalOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->total_orden_pago, 2);
    }

    public function getImpuestoFormatAttribute()
    {
        return '$ ' . number_format($this->impuesto, 2);
    }

    public function getMontoAPagarAttribute()
    {
        $monto_pagar = $this->total_orden_pago + $this->anticipo_a_liberar;
        if($this->configuracion->retenciones_antes_iva == 0){
            $monto_pagar-=$this->retenciones->sum("importe");
            $monto_pagar-=$this->IVARetenido;
            $monto_pagar+=$this->liberaciones->sum("importe");
        }
        if($this->configuracion->desc_pres_mat_antes_iva == 0){
            $monto_pagar-=$this->descuentos->sum("importe");
        }
        if($this->configuracion->ret_fon_gar_antes_iva == 0){
            $monto_pagar-=$this->retencion_fondo_garantia_orden_pago;
        }
        return $monto_pagar;
    }

    public function getMontoAPagarFormatAttribute()
    {
        return '$ ' . number_format($this->monto_a_pagar, 2);
    }

    /**
     * Este método implementa la lógica actualización de control de obra del procedimiento almacenado sp_aplicar_pagos
     * y se detona al registrar una orden de pago relacionada a una factura que ampara una estimación
     */
    public function actualizaControlObra(ItemFactura $item_factura, OrdenPago $orden_pago)
    {
        $importe = round($orden_pago->monto * -1 * $item_factura->proporcion_item, 2);
        $tipo_cambio = $item_factura->factura->tipo_cambio;
        $monto_total = 0;
        if ($this->movimientos) {
            $monto_total = $this->movimientos->sum("monto_total");
        }
        if ($monto_total > 0) {
            if ($this->movimientos) {
                foreach ($this->movimientos as $movimiento) {
                    $movimiento->monto_pagado = round(($movimiento->monto_pagado + $movimiento->monto_total * $importe
                        * $tipo_cambio / $monto_total), 2);
                    $movimiento->save();
                }
            }
        }
    }

    public function getImporteRetencionConIva($retencion)
    {
        return $retencion * 1.16;
    }

    /**
     * Obtener el valor del Importe para crear o editar la retención.
     * @return float|int
     */
    private function importeRetencionFondoGarantia()
    {
        $importe = $this->items()->sum('importe') * ($this->retencion / 100);
        /**
         * Validar: SI es después de IVA se debe agregar el IVA al importe a registrar.
         */
        $configuracion_estimacion = ConfiguracionEstimacion::pluck('ret_fon_gar_antes_iva');

        if(!is_null($configuracion_estimacion) && $configuracion_estimacion[0] == 0)
        {
            $importe =  $this->getImporteRetencionConIva($importe);
        }
        return $importe;
    }

    /**
     * Validaciones para revertir la retención del fondo de garantía al eliminar la estimación.
     */
    public function cancelarRetencion()
    {
        if($this->retencion != 0 && $this->retencion_fondo_garantia) {
            $movimiento_retencion = $this->retencion_fondo_garantia->generaCancelacionMovimientoRetencion();

            $this->retencion_fondo_garantia->movimientos->movimiento_general()->create(
                [
                    'id_fondo_garantia' => $this->id_antecedente,
                    'id_tipo_movimiento' => 3,
                    'id_movimiento_retencion' => $movimiento_retencion->id,
                    'observaciones' => 'Eliminación de la estimación ' . $this->numero_folio_format,
                    'importe' => $this->retencion_fondo_garantia->importe
                ]
            );
            $this->retencion_fondo_garantia()->update(
                [
                    'importe' => 0
                ]
            );
        }
    }
}
