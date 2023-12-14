<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 04:24 PM
 */

namespace App\Models\CADECO;
use App\CSV\Contratos\EstimacionLayout;
use App\Facades\Context;
use App\Models\CADECO\Acarreos\ConciliacionEstimacion;
use App\Models\CADECO\Compras\ItemContratista;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Estimaciones\EstimacionEliminada;
use App\Models\CADECO\Estimaciones\EstimacionPartidaEliminada;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Models\CADECO\SubcontratosEstimaciones\FolioPorSubcontrato;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Penalizacion;
use App\Models\CADECO\SubcontratosEstimaciones\PenalizacionLiberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Models\CADECO\SubcontratosFG\RetencionFondoGarantia;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Estimacion extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;
    public const OPCION_ANTECEDENTE = 2;
    public const TIPO = 52;
    public const OPCION = 0;
    public const NOMBRE = "Estimaciones";
    public const ICONO = "fa fa-building";

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
        'id_usuario',
        'referencia',
        'id_usuario',
        'retencion',
        'id_empresa',
        'id_moneda',
        'numero_folio'
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

    public function subcontrato_sgc()
    {
        return $this->belongsTo(Subcontrato::class, 'id_antecedente', 'id_transaccion')->withoutGlobalScopes();
    }

    public function subcontratoSinGlobalScope()
    {
        return $this->belongsTo(Subcontrato::class, 'id_antecedente', 'id_transaccion')->withoutGlobalScopes();
    }

    public function subcontratoSinGlobal()
    {
        return $this->belongsTo(Transaccion::class, 'id_antecedente','id_transaccion')->withoutGlobalScopes()->where('tipo_transaccion', '=', 51)->where('opciones', '=', 2);
    }

    public function itemsXContratistas()
    {
        return $this->hasMany(ItemContratista::class, 'id_empresa', 'id_empresa');
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

    public function items()
    {
        return $this->hasMany(ItemEstimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function penalizaciones()
    {
        return $this->hasMany(Penalizacion::class, 'id_transaccion');
    }

    public function penalizacionLiberaciones()
    {
        return $this->hasMany(PenalizacionLiberacion::class, 'id_transaccion');
    }

    public function movimientos()
    {
        return $this->hasManyThrough(Movimiento::class, ItemEstimacion::class, "id_transaccion", "id_item", "id_transaccion", "id_item");
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
        return $this->hasMany(ItemEstimacion::class, 'id_transaccion', 'id_transaccion');
    }

    /*public function facturas()
    {
        return $this->hasMany(Factura::class, 'id_antecedente', 'id_transaccion');
    }*/

    public function facturas()
    {
        return $this->hasManyThrough(Factura::class,FacturaPartida::class,"id_antecedente","id_transaccion","id_transaccion","id_transaccion")
            ->distinct();
    }

    public function prepoliza()
    {
        return $this->belongsTo(Poliza::class, 'id_transaccion', 'id_transaccion_sao');
    }

    public function estimacionEliminada()
    {
        return $this->belongsTo(EstimacionEliminada::class, 'id_transaccion');
    }

    public function partidasRelacionadas()
    {
        return $this->hasMany(ItemEstimacion::class, 'id_transaccion', 'id_antecedente');
    }

    public function itemsReferenciados()
    {
        return $this->hasMany(Item::class, 'id_antecedente','id_transaccion');
    }

    public function conciliacionAcarreos()
    {
        return $this->hasOne(ConciliacionEstimacion::class, "id_estimacion", "id_transaccion");
    }

    public function fondoGarantiaSinContexto()
    {
        return $this->belongsTo(\App\Models\CADECO\SubcontratosFG\FondoGarantia::class,  'id_antecedente','id_subcontrato')->withoutGlobalScopes();
    }

    /**
     * Scope
     */

    /**
     * Acciones
     */
    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $data['anticipo'] = $this->calculaAnticipo($data['id_antecedente'], $data['conceptos']);
            $estimacion = $this->create($data);
            $estimacion->estimaConceptos($data['conceptos']);
            $estimacion->recalculaDatosGenerales();
            DB::connection('cadeco')->commit();
            return $estimacion;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function estimaConceptos($conceptos)
    {
        foreach ($conceptos as $concepto) {
            $pu = Item::where('id_transaccion', '=', $this->id_antecedente)
                ->where('id_concepto', '=', $concepto['item_antecedente'])
                ->first()->precio_unitario;

            $this->items()->create([
                'id_transaccion' => $this->id_transaccion,
                'id_antecedente' => $this->id_antecedente,
                'item_antecedente' => $concepto['item_antecedente'],
                'id_concepto' => $concepto['id_concepto'],
                'cantidad' => $concepto['cantidad'],
                'cantidad_material' => 0,
                'cantidad_mano_obra' => 0,
                'importe' => $concepto['importe'],
                'precio_unitario' => $pu,
                'precio_material' => 0,
                'precio_mano_obra' => 0
            ]);
        }
    }

    public function creaSubcontratoEstimacion()
    {
        \App\Models\CADECO\SubcontratosEstimaciones\Estimacion::query()->create([
            'IDEstimacion' => $this->id_transaccion,
            'NumeroFolioConsecutivo' => $this->generaFolioConsecutivo()
        ]);
    }

    public static function calcularFolio()
    {
        $est = Transaccion::where('tipo_transaccion', '=', 52)->orderBy('numero_folio', 'DESC')->first();
        return $est ? $est->numero_folio + 1 : 1;
    }

    private function generaFolioConsecutivo()
    {
        if(!is_null(Context::getIdObra())) {
            $folio = FolioPorSubcontrato::where('IDSubcontrato', '=', $this->id_antecedente)->first();
        }else{
            $folio = FolioPorSubcontrato::withoutGlobalScopes()->where('IDSubcontrato', '=', $this->id_antecedente)->first();
        }

        if ($folio) {
            $folio->UltimoFolio += 1;
            $folio->save();
        } else {
            $folio = FolioPorSubcontrato::create([
                'IDSubcontrato' => $this->id_antecedente,
                'UltimoFolio' => 1,
                'IDObra' => $this->id_obra
            ]);
        }
        return $folio->UltimoFolio;
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
                        'importe' => $this->retencion_fondo_garantia_orden_pago
                    ]
                );
            } else {
                $this->retencion_fondo_garantia->importe = $this->retencion_fondo_garantia_orden_pago;
                $this->retencion_fondo_garantia->save();
                $this->retencion_fondo_garantia->generaMovimientoRegistro();
            }
        } else {
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
        $this->save();

        $this->recalculaDatosGenerales();

        DB::connection('cadeco')->update("EXEC [dbo].[sp_aprobar_transaccion] {$this->id_transaccion}");
        if ($this->subcontrato->retencion && $this->subcontrato->retencion > 0) {
            $this->generaRetencion();
        }
        return $this;
    }

    public function anticipoAmortizacion($data)
    {
        if ($data <= $this->sumaImportes) {
            if ($this->sumaImportes == 0 || $this->sumaImportes == null) {
                $this->anticipo = 0;
                $this->save();
            } else {
                if ($this->subcontrato->anticipo != 0) {
                    $this->anticipo = ($data / $this->sumaImportes) * 100;
                    $this->save();
                } else {
                    throw new \Exception('No se puede actualizar la amortización de anticipo de está estimación porque el Subcontrato no tiene porcentaje de anticipo definido.');
                }

            }
            $this->recalculaDatosGenerales();
            return $this->subcontratoAEstimar();
        } else {
            throw new \Exception('El importe de la amortización no puede ser mayor al importe de la estimación.');
        }
    }

    public function revertirAprobacion()
    {
        if ($this->estado == 2) {
            throw new \Exception('La estimacion se encuentra revisada contra factura, no es posible revertir la aprobación.');
        }

        DB::connection('cadeco')->update("EXEC [dbo].[sp_revertir_transaccion] {$this->id_transaccion}");
        $this->recalculaDatosGenerales();

        return $this;
    }

    public function getReferenciaRevisionAttribute()
    {
        return $this->subcontrato_sgc->referencia;
    }

    public function getMontoRevisionAttribute()
    {
        return number_format($this->suma_importes - $this->autorizado, 2, ".", "");
    }

    public function getFolioRevisionFormatAttribute()
    {
        return 'EST ' . $this->numero_folio_format;
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
        return '$ ' . number_format($this->suma_importes, 2, ".", ",");
    }

    public function getMontoRevisionFormatAttribute()
    {
        return '$ ' . number_format($this->monto_revision, 2, ".", ",");
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

    public function getTipoCambioAttribute(){
        switch((int)$this->id_moneda){
            case 1:
                return 1;
                break;
            case 2:
                return $this->TcUsd;
                break;
            case 3:
                return $this->TcEuro;
                break;


        }
    }

    public function getMontoAnticipoAplicadoAttribute()
    {
        return $this->suma_importes * (($this->anticipo) / 100);
    }

    public function getMontoAnticipoAplicadoFormatAttribute()
    {
        return '$ ' . number_format($this->monto_anticipo_aplicado, 2);
    }

    public function getRetenidoAnteriorAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()
            ->where('fecha', '<=', $this->fecha)
            ->where('estado', '>=', 1)
            ->where("id_transaccion", '<>', $this->id_transaccion)
            ->get();

        $sumatoria = 0;
        foreach ($estimaciones_anteriores as $estimacion) {
            $sumatoria += $estimacion->retencion_fondo_garantia_orden_pago;
        }
        return $sumatoria;
    }

    public function getRetenidoOrigenAttribute()
    {
        return $this->retenido_anterior + $this->retencion_fondo_garantia_orden_pago;
    }

    public function getConfiguracionAttribute()
    {
        $configuracion = $this->obra->configuracionEstimaciones;
        if (!$configuracion) {
            $configuracion = ConfiguracionEstimacion::create([
                'penalizacion_antes_iva' => 1,
                'retenciones_antes_iva' => 1,
                'ret_fon_gar_antes_iva' => 1,
                'desc_pres_mat_antes_iva' => 1,
                'desc_otros_prest_antes_iva' => 0,
                'ret_fon_gar_con_iva' => 0,
                'amort_anticipo_antes_iva' => 1
            ]);
            $this->refresh();
        }
        return $configuracion;
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->respaldar($motivo);
            foreach ($this->items()->get() as $item) {
                $item->delete();
            }
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

        $item_relacionados = Item::where('id_antecedente', '=', $this->id_transaccion)->first();
        if ($item_relacionados) {
            $transaccion = Transaccion::where('id_transaccion', '=', $item_relacionados->id_transaccion)->withoutGlobalScopes()->first();
            if ($transaccion) {
                $mensaje = $mensaje . "-Contiene items relacionados en " . $transaccion->tipo->Descripcion . ". \n";
            } else {
                $mensaje = $mensaje . "-Contiene items relacionados a otra transacción \n";
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
        $subtotal = $this->suma_importes - $this->monto_anticipo_aplicado;
        if ($this->configuracion->retenciones_antes_iva == 1) {
            $subtotal -= $this->retenciones->sum("importe");
            $subtotal += $this->liberaciones->sum("importe");
        }
        if ($this->configuracion->desc_pres_mat_antes_iva == 1) {
            $subtotal -= $this->descuentos->sum("importe");
        }
        if ($this->configuracion->ret_fon_gar_antes_iva == 1) {
            $subtotal -= $this->retencion_fondo_garantia_orden_pago;
        }
        if($this->configuracion->penalizacion_antes_iva == 1)
        {
            $subtotal -= $this->suma_penalizaciones;
            $subtotal += $this->suma_penalizaciones_liberadas;
        }
        return $subtotal;
    }

    public function getSubtotalOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->subtotal_orden_pago, 2);
    }

    public function getIvaOrdenPagoAttribute()
    {
        if ($this->subcontratoSinGlobal->impuesto != 0)
        {
            return $this->subtotal_orden_pago * ($this->subcontratoSinGlobal->tasa_iva / 100);
        } else {
            return 0;
        }
    }

    public function getIvaOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->iva_orden_pago, 2);
    }

    public function getSumaDeductivasAttribute()
    {
        return $this->descuentos->sum('importe');
    }

    public function getSumaDeductivasFormatAttribute()
    {
        return '$ ' . number_format($this->suma_deductivas, 2);
    }

    public function getSumaRetencionesAttribute()
    {
        return $this->retenciones->sum('importe');
    }

    public function getSumaRetencionesFormatAttribute()
    {
        return '$ ' . number_format($this->suma_retenciones, 2);
    }

    public function getSumaLiberacionesAttribute()
    {
        return $this->liberaciones->sum('importe');
    }

    public function getSumaLiberacionesFormatAttribute()
    {
        return '$ ' . number_format($this->suma_liberaciones, 2);
    }

    public function getTotalOrdenPagoAttribute()
    {
        $total = ($this->subtotal_orden_pago + $this->iva_orden_pago) - $this->IVARetenido;

        if($this->porcentaje_isr_retenido && $this->porcentaje_isr_retenido > 0){
            $total -= $this->monto_isr_retenido;
        }
        return $total;
    }

    # retencion_fondo_garantia_orden_pago_format
    public function getRetencionFondoGarantiaOrdenPagoAttribute()
    {
        if ($this->configuracion->ret_fon_gar_antes_iva == 0) {
            if ($this->configuracion->ret_fon_gar_con_iva == 1) {
                return $this->suma_importes * ($this->retencion / 100) * 1.16;
            } else {
                return $this->suma_importes * ($this->retencion / 100);
            }
        } else {
            return $this->suma_importes * ($this->retencion / 100);
        }
    }

    public function getRetencionFondoGarantiaOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->retencion_fondo_garantia_orden_pago, 2);
    }

    public function getAnticipoALiberarAttribute()
    {
        return $this->subcontratoEstimacion ? $this->subcontratoEstimacion->ImporteAnticipoLiberar : 0;
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
        if ($this->configuracion->retenciones_antes_iva == 0) {
            $monto_pagar -= $this->retenciones->sum("importe");
            // $monto_pagar -= $this->IVARetenido;
            $monto_pagar += $this->liberaciones->sum("importe");
        }
        if ($this->configuracion->desc_pres_mat_antes_iva == 0) {
            $monto_pagar -= $this->descuentos->sum("importe");
        }
        if ($this->configuracion->ret_fon_gar_antes_iva == 0) {
            $monto_pagar -= $this->retencion_fondo_garantia_orden_pago;
        }
        $monto_pagar -= $this->retencionIVA_2_3;
        if($this->configuracion->penalizacion_antes_iva == 0)
        {
            $monto_pagar -= $this->suma_penalizaciones;
            $monto_pagar += $this->suma_penalizaciones_liberadas;
        }
        return $monto_pagar;
    }

    public function getMontoAPagarFormatAttribute()
    {
        return '$ ' . number_format($this->monto_a_pagar, 2);
    }

    public function getIvaRetenidoCalculadoAttribute()
    {
        return $this->IVARetenido + $this->retencionIVA_2_3;
    }

    public function getIsrRetenidoCalculadoAttribute()
    {
        return $this->monto_isr_retenido;
    }

    public function getIvaRetenidoFormatAttribute()
    {
        return '$ ' . number_format($this->iva_retenido_calculado, 2);
    }

    public function getIvaRetenidoPorcentajeAttribute()
    {
        if ($this->suma_importes > 0) {
            return number_format($this->IVARetenido * 100 / $this->suma_importes, 2) . " %";
        } else {
            return "0 %";
        }
    }

    public function getPorcentajeIsrRetenidoFormatAttribute(){
        if($this->porcentaje_isr_retenido && $this->porcentaje_isr_retenido > 0){
            return number_format($this->porcentaje_isr_retenido, 2) . " %";
        }
        return "0 %";
    }

    public function getTasaIvaAttribute()
    {
        if($this->subtotal_orden_pago != 0) {
            return $this->impuesto / $this->subtotal_orden_pago;
        }else{
            return 0.16;
        }
    }

    public function getTasaIvaFormatAttribute()
    {
        return number_format($this->tasa_iva*100, 0, '.', '');
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

    public function recalculaDatosGenerales()
    {
        $this->refresh();

        $this->monto = $this->monto_a_pagar;
        $this->saldo = $this->monto_a_pagar;
        $this->impuesto = $this->iva_orden_pago;
        $this->save();

        $this->subcontratoEstimacion->PorcentajeFondoGarantia = ($this->retencion);
        $this->subcontratoEstimacion->ImporteFondoGarantia = $this->retencion_fondo_garantia_orden_pago;
        $this->subcontratoEstimacion->save();
    }

    /**
     * Validaciones para revertir la retención del fondo de garantía al eliminar la estimación.
     */
    public function cancelarRetencion()
    {
        if ($this->retencion != 0 && $this->retencion_fondo_garantia) {
            $movimiento_retencion = $this->retencion_fondo_garantia->generaCancelacionMovimientoRetencion();

            $this->retencion_fondo_garantia->movimientos->movimiento_general()->create(
                [
                    'id_fondo_garantia' => $this->id_antecedente,
                    'id_tipo_movimiento' => 3,
                    'id_movimiento_retencion' => $movimiento_retencion->id,
                    'observaciones' => 'Eliminación de retención en la estimación ' . $this->numero_folio_format,
                    'importe' => $this->retencion_fondo_garantia->importe
                ]
            );
            $this->retencion_fondo_garantia->importe = 0;
            $this->retencion_fondo_garantia->save();
        }
    }

    public function registrarIVARetenido($retenciones)
    {
        if($this->subtotal_orden_pago == 0) abort(403, 'La estimación no cuenta con volumen registrado.');

        if($retenciones['retencionIVA_2_3'] != null && $retenciones['retencionIVA_2_3'] >= 0){
            $iva_o_p = $this->subtotal_orden_pago * 0.16;
            if(abs((($iva_o_p / 3) * 2) -  $retenciones['retencionIVA_2_3'] ) > 0.99 && $retenciones['retencionIVA_2_3'] > 0){
                abort(403, 'La retención de IVA no es 2/3');
            }
            $this->retencionIVA_2_3 = $retenciones['retencionIVA_2_3'];
        }

        if($retenciones['retencion4'] != null && $retenciones['retencion4'] > 0){
            $porcentaje = $retenciones['retencion4'] * 100 / $this->suma_importes;
            if ($porcentaje <= 3.9999 || $porcentaje >= 4.0001) {
                abort(403, 'La retención de IVA no es del 4%');
            }
        }
        if($retenciones['retencion6'] != null && $retenciones['retencion6'] > 0){
            $porcentaje = $retenciones['retencion6'] * 100 / $this->subtotal_orden_pago;
            if ($porcentaje <= 5.9999 || $porcentaje >= 6.0001) {
                abort(403, 'La retención de IVA no es del 6%');
            }
        }

        $retencion_registrada_4 = $this->retencion_iva4;
        $retencion_registrada_6 = $this->retencion_iva6;
        $retenciones['retencion4'] != null? $retencion_registrada_4 = $retenciones['retencion4']:'';
        $retenciones['retencion6'] != null? $retencion_registrada_6 = $retenciones['retencion6']:'';

        $retencion = $retencion_registrada_4 + $retencion_registrada_6;

        $this->IVARetenido = $retencion;
        $this->save();
        $this->recalculaDatosGenerales();
        return $this;
    }

    public function registrarISRRetenido($data){
        if($data['retencionISR125'] && $data['retencionISR10']){
            abort(403, 'Favor de solo ingresar una retención.');
        }

        if($data['retencionISR125']){
            if(abs(($this->subtotal_orden_pago * 0.0125) - $data['retencionISR125']) > 0.99){
                abort(403, 'La retención de ISR no es del 1.25%');
            }
            $this->porcentaje_isr_retenido = 1.25;
            $this->monto_isr_retenido = $data['retencionISR125'];
        }
        if($data['retencionISR10']){
            if(abs(($this->subtotal_orden_pago * 0.1) - $data['retencionISR10']) > 0.99){
                abort(403, 'La retención de ISR no es del 10%');
            }
            $this->porcentaje_isr_retenido = 10;
            $this->monto_isr_retenido = $data['retencionISR10'];
        }
        $this->save();
        return $this;
    }

    /**
     * Obtener estimación con las partidas ordenadas dependiendo los niveles de los contratos.
     * @return array
     */
    public function subcontratoAEstimar()
    {
        return [
            'fecha_inicial'           => $this->getOriginal('cumplimiento'),
            'fecha_final'             => $this->vencimiento,
            'fecha_orig'              => $this->fecha,
            'fecha'                   => $this->fecha_format,
            'razon_social'            => $this->empresa->razon_social,
            'moneda'                  => $this->moneda->nombre,
            'observaciones'           => $this->observaciones,
            'folio'                   => $this->numero_folio_format,
            'subtotal'                => $this->subtotal_orden_pago,
            'iva'                     => $this->iva_orden_pago,
            'retencion_iva_tasa'      => $this->iva_retenido_porcentaje,
            'retencion_iva_monto'     => $this->iva_retenido,
            'retencion_iva_monto_format'     => $this->iva_retenido_format,
            'retencion_isr_tasa'      => $this->porcentaje_isr_retenido_format,
            'retencion_isr_monto'     => $this->monto_isr_retenido,
            'retencion_isr_monto_format'     => $this->monto_isr_retenido_format,
            'total'                   => $this->total_orden_pago,
            'folio_consecutivo'       => $this->subcontratoEstimacion ? $this->subcontratoEstimacion->folio_consecutivo_format : '',
            'folio_consecutivo_num'   => $this->subcontratoEstimacion ? $this->subcontratoEstimacion->NumeroFolioConsecutivo : '',
            'id_empresa'              => $this->empresa->id_empresa,
            'anticipo_format'         => $this->anticipo_format,
            'monto_anticipo_aplicado' => $this->monto_anticipo_aplicado,
            'estado'                  => $this->estado,
            'estado_format'           => $this->estado_descripcion,
            'subcontrato'             => $this->subcontrato->subcontratoParaEstimar($this->id_transaccion)
        ];
    }

    /**
     * Editar la estimación
     * @param $datos
     * $datos = [
     *      fecha_inicial,
     *      fecha_final,
     *      observaciones,
     *      partidas ]
     * @return $this
     * @throws \Exception
     */
    public function editar($datos)
    {
        try {
            $format = 'd/m/Y';
            if($fecha_inicial = DateTime::createFromFormat($format, $datos['fecha_inicial'])){
                $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
            }else{
                $fecha_inicial = New DateTime($datos['fecha_inicial']);
                $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
            }
            if($fecha_final = DateTime::createFromFormat($format, $datos['fecha_final'])){
                $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
            }else{
                $fecha_final = New DateTime($datos['fecha_final']);
                $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
            }

            DB::connection('cadeco')->beginTransaction();

            foreach ($datos['partidas'] as $partida) {

                if (array_key_exists('id', $partida)) {

                    /**
                     * Se edita item existente.
                     */
                    if ($partida['id_item_estimacion'] != 0) {
                        $item = $this->items()->where('id_item', '=', $partida['id_item_estimacion'])->first();

                        $item->update([
                            'cantidad' => $partida['cantidad_estimacion'],
                            'importe' => $partida['importe_estimacion']
                        ]);
                    }

                    /**
                     * Se crea un item nuevo.
                     */
                    if ($partida['id_item_estimacion'] == 0 && ($partida['cantidad_estimacion'] != 0 || $partida['importe_estimacion'] != 0)) {
                        $this->items()->create([
                            'id_transaccion' => $this->id_transaccion,
                            'id_antecedente' => $this->id_antecedente,
                            'item_antecedente' => $partida['id_concepto'],
                            'id_concepto' => $partida['id_destino'],
                            'cantidad' => $partida['cantidad_estimacion'],
                            'cantidad_material' => 0,
                            'cantidad_mano_obra' => 0,
                            'importe' => $partida['importe_estimacion'],
                            'precio_unitario' => $partida['precio_unitario_subcontrato'],
                            'precio_material' => 0,
                            'precio_mano_obra' => 0
                        ]);
                    }
                }
            }

            $this->update([
                'cumplimiento' => $fecha_inicial->format("Y-m-d"),
                'vencimiento' => $fecha_final->format("Y-m-d"),
                'observaciones' => $datos['observaciones']
            ]);

            $this->recalculaDatosGenerales();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
            throw $e;
        }
    }

    public function getAnticipoFormatAttribute()
    {
        return number_format(abs($this->anticipo), 4) . '%';
    }

    public function descuentosPartidas()
    {
        $array = array();
        $importe_total_original = 0;
        $importe_descuento = 0;
        $importe_descuento_anterior = 0;
        $importe_porEstimar = 0;
        $importe_acumulado = 0;
        $itemxcontratistas = $this->itemsXContratistas()->pluck('id_item');
        $items = Item::whereIn('id_item', $itemxcontratistas)->selectRaw('id_material, sum(cantidad) as cantidad,  sum(importe) as importe, sum(importe)/sum(cantidad) as precio_unitario')->groupBy('id_material')->get();

        foreach ($items as $k => $a) {
            $importe_total_original += $a->importe;
            $descuento = Descuento::where('id_transaccion', '=', $this->id_transaccion)->where('id_material', '=', $a->id_material)->first();
            $descuento_antertior = Descuento::join('transacciones', 'transacciones.id_transaccion', 'descuento.id_transaccion')
                ->where('transacciones.id_transaccion', '!=', $this->id_transaccion)
                //->where('id_antecedente', '=', $this->id_antecedente)
                ->where('id_empresa', '=', $this->id_empresa)
                ->where('descuento.id_material', '=', $a->id_material)
                ->where('id_obra', '=', $this->id_obra)
                ->where('estado', '>', 0)
                ->selectRaw('sum(descuento.cantidad) as cantidad,  sum(descuento.importe) as importe')->first();

            $importe_descuento += $descuento ? $descuento->importe : 0;
            $importe_descuento_anterior += $descuento_antertior->importe ? $descuento_antertior->importe : 0;
            $importe_porEstimar += ($a->importe - (($descuento_antertior->importe ? $descuento_antertior->importe : 0) + ($descuento ? $descuento->importe : 0)));
            $importe_acumulado += ($descuento_antertior->importe ? $descuento_antertior->importe : 0) + ($descuento ? $descuento->importe : 0);

            $array[$k] = [
                'id_material' => $a->id_material,
                'unidad' => $a->material->unidad,
                'descripcion' => $a->material->descripcion,
                'precio_unitario' => $a->precio_unitario,
                'cantidad_original' => $a->cantidad,
                'importe_original' => $a->importe,
                'cantidad_descuento' => $descuento ? $descuento->cantidad : 0,
                'importe_descuento' => $descuento ? $descuento->importe : 0,
                'precio_descuento' => $descuento ? $descuento->precio : 0,
                'cantidad_descuento_anterior' => $descuento_antertior->cantidad ? $descuento_antertior->cantidad : 0,
                'importe_descuento_anterior' => $descuento_antertior->importe ? $descuento_antertior->importe : 0,
                'cantidad_a_esta_estimacion' => ($descuento_antertior->cantidad ? $descuento_antertior->cantidad : 0) + ($descuento ? $descuento->cantidad : 0),
                'importe_a_esta_estimacion' => ($descuento_antertior->importe ? $descuento_antertior->importe : 0) + ($descuento ? $descuento->importe : 0),
                'cantidad_descuento_porEstimar' => ($a->cantidad - (($descuento_antertior->cantidad ? $descuento_antertior->cantidad : 0) + ($descuento ? $descuento->cantidad : 0))),
                'importe_descuento_porEstimar' => ($a->importe - (($descuento_antertior->importe ? $descuento_antertior->importe : 0) + ($descuento ? $descuento->importe : 0)))
            ];
        }
        return array(
            'importe_total_original' => $importe_total_original,
            'importe_descuento' => $importe_descuento,
            'importe_descuento_anterior' => $importe_descuento_anterior,
            '$importe_acumulado' => $importe_acumulado,
            'importe_porEstimar' => $importe_porEstimar,
            'partidas_descuento' => $array
        );
    }

    public function getAnticipoAnteriorAttribute()
    {
        $anticipo = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
                                        ->where('numero_folio', '<', $this->numero_folio)
                                        ->where('estado', '>=', 0)->get();

        foreach($estimaciones_anteriores as $estimacion){
            $anticipo += $estimacion->monto_anticipo_aplicado;
        }
        return $anticipo;
    }

    public function getFondoGarantiaAcumuladoAnteriorAttribute()
    {
        $fondo = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
                                        ->where('numero_folio', '<', $this->numero_folio)
                                        ->where('estado', '>=', 0)->get();

        foreach($estimaciones_anteriores as $estimacion){
            $fondo += $estimacion->retencion_fondo_garantia_orden_pago;
        }
        return $fondo;
    }

    public function getPorcentajeIvaAttribute()
    {
        return ($this->iva_orden_pago / $this->subtotal_orden_pago) * 100;
    }

    public function getRetencionIva4Attribute(){
        if($subtotal = $this->subtotal_orden_pago){
            $porcentaje = $this->IVARetenido * 100 / $subtotal;
            if((int)round($porcentaje) == 10) return $this->IVARetenido * .4;
            if((int)round($porcentaje) == 4) return $this->IVARetenido;
        }
        return 0;
    }

    public function getRetencionIva4FormatAttribute(){
        return '$ ' . number_format($this->retencion_iva4, 2);
    }

    public function getRetencionIva6Attribute(){
        if($subtotal = $this->subtotal_orden_pago){
            $porcentaje = $this->IVARetenido * 100 / $subtotal;
            if((int)round($porcentaje) == 10) return $this->IVARetenido * .6;
            if((int)round($porcentaje) == 6) return $this->IVARetenido;
        }
        return 0;
    }

    public function getRetencionIva6FormatAttribute(){
        return '$ ' . number_format($this->retencion_iva6, 2);
    }

    public function getRetencionIva23FormatAttribute(){
        return '$ ' . number_format($this->retencionIVA_2_3, 2);
    }

    public function getMontoIsrRetenidoFormatAttribute(){
        return '$ ' . number_format($this->monto_isr_retenido, 2);
    }

    public function getEstadoDescripcionAttribute()
    {
        switch ($this->estado) {
            case 0:
                return 'Registrada';
                break;
            case 1:
                return 'Aprobada';
                break;
            case 2:
                return 'Revisada';
                break;
            default:
                return 'Desconocido';
                break;
        }
    }

    public function getIvaRetenidoCalculadoAnteriorAttribute()
    {
        $iva_retenido = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();

        foreach($estimaciones_anteriores as $estimacion){
            $iva_retenido += $estimacion->iva_retenido_calculado;
        }
        return $iva_retenido;
    }

    public function getIsrRetenidoCalculadoAnteriorAttribute()
    {
        $isr_retenido = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();

        foreach($estimaciones_anteriores as $estimacion){
            $isr_retenido += $estimacion->isr_retenido_calculado;
        }
        return $isr_retenido;
    }

    public function getAcumuladoPenalizacionesAnterioresAttribute()
    {
        $acumulado = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();

        foreach ($estimaciones_anteriores as $estimacion) {
            $acumulado += $estimacion->suma_penalizaciones;
        }
        return $acumulado;
    }

    public function getAcumuladoPenalizacionesLiberadaAnterioresAttribute()
    {
        $acumulado = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();
        foreach ($estimaciones_anteriores as $estimacion) {
            $acumulado += $estimacion->suma_penalizaciones_liberadas;
        }
        return $acumulado;
    }

    public function getAcumuladoRetencionAnterioresAttribute()
    {
        $acumulado = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();
        foreach ($estimaciones_anteriores as $estimacion) {
            $acumulado += $estimacion->suma_retenciones;
        }
        return $acumulado;
    }

    public function getAcumuladoLiberacionAnterioresAttribute()
    {
        $acumulado = 0;
        $estimaciones_anteriores = $this->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();
        foreach ($estimaciones_anteriores as $estimacion) {
            $acumulado += $estimacion->suma_liberaciones;
        }
        return $acumulado;
    }

    public function getSumaPenalizacionesAttribute()
    {
        return $this->penalizaciones->sum('importe');
    }

    public function getSumaPenalizacionesLiberadasAttribute()
    {
        return $this->penalizacionLiberaciones->sum('importe');
    }

    public function getSumaPenalizacionesFormatAttribute()
    {
        return '$ ' . number_format($this->suma_penalizaciones, 2);
    }

    public function getSumaPenalizacionesLiberadasFormatAttribute()
    {
        return '$ ' . number_format($this->suma_penalizaciones_liberadas, 2);
    }

    public function getRestaImportesAmortizacionAttribute()
    {
        return $this->suma_importes - $this->monto_anticipo_aplicado;
    }

    /**
     * Ejecuta lógica: sp_revertir_transaccion
     * Validaciones para revertir la estimación
     * @param $estimacion
     */
    private function revertir_estimacion()
    {
        if (is_null($this->itemsReferenciados()))
        {
            abort(400, "Esta estimación ".$this->numero_folio_format." se encuentra asociada a otras transacciones.");
        }

        foreach ($this->items as $item)
        {
            $item->movimiento->delete();
        }
        $this->estado = 0;
        $this->impreso = 0;
        $this->saldo = $this->monto;
        $this->save();
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = Estimacion::NOMBRE;
        $datos["tipo_numero"] = Estimacion::TIPO;
        $datos["icono"] = Estimacion::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        $estimacion = $this;

        #CONTRATOS PROYECTADOS
        if($this->subcontratoSinGlobalScope){
            if($this->subcontratoSinGlobalScope->contratoProyectadoSinGlobalScope){
                $relaciones[$i] = $this->subcontratoSinGlobalScope->contratoProyectadoSinGlobalScope->datos_para_relacion;
                $i++;
            }
        }

        #PRESUPUESTOS
        if($this->subcontrato){
            $presupuestos = $this->subcontratoSinGlobalScope->presupuestos;
            foreach($presupuestos as $presupuesto)
            {
                if($presupuesto){
                    $relaciones[$i] = $presupuesto->datos_para_relacion;
                    $i++;
                }
            }
        }

        #SUBCONTRATO
        $subcontrato = $this->subcontratoSinGlobalScope;
        if($this->subcontratoSinGlobalScope) {
            $relaciones[$i] = $subcontrato->datos_para_relacion;
            $i++;

            #POLIZA DE SUBCONTRATO
            if($subcontrato->poliza){
                $relaciones[$i] = $subcontrato->poliza->datos_para_relacion;
                $i++;
            }
            #FACTURA DE SUBCONTRATO
            foreach ($subcontrato->facturas as $factura){
                $relaciones[$i] = $factura->datos_para_relacion;
                $i++;
                #POLIZA DE FACTURA DE SUBCONTRATO
                if($factura->poliza){
                    $relaciones[$i] = $factura->poliza->datos_para_relacion;
                    $i++;
                }
                #PAGO DE FACTURA DE SUBCONTRATO
                foreach ($factura->ordenesPago as $orden_pago){
                    if($orden_pago->pago){
                        $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                        $i++;
                        #POLIZA DE PAGO DE FACTURA DE SUBCONTRATO
                        if($orden_pago->pago->poliza){
                            $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                            $i++;
                        }
                    }
                }
            }
            #SOLICITUD DE CAMBIO A SUBCONTRATO
            foreach ($subcontrato->solicitudesCambio as $solicitud_cambio){
                $relaciones[$i] = $solicitud_cambio->datos_para_relacion;
                $i++;
            }
        }




        #ESTIMACION
        $relaciones[$i] = $estimacion->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        #FACTURA DE ESTIMACION
        foreach ($estimacion->facturas as $factura){
            $relaciones[$i] = $factura->datos_para_relacion;
            $i++;

            #POLIZA DE FACTURA DE ESTIMACION
            if($factura->poliza){
                $relaciones[$i] = $factura->poliza->datos_para_relacion;
                $i++;
            }

            #PAGO DE FACTURA DE ESTIMACION
            foreach ($factura->ordenesPago as $orden_pago){
                if($orden_pago->pago){
                    $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                    $i++;
                    #POLIZA DE PAGO DE FACTURA DE ESTIMACION
                    if($orden_pago->pago->poliza){
                        $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                        $i++;
                    }
                }
            }
        }

        $orden1 = array_column($relaciones, 'orden');
        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function getOrigenAcarreosAttribute()
    {
        try{
            if($this->conciliacionAcarreos->id >0)
                return true;
            else
                return false;
        } catch (\Exception $e){
            return false;
        }

    }

    public function getEstimacionFolioConsecutivoFormatAttribute(){
        if($this->subcontratoEstimacion){
            return $this->subcontratoEstimacion->folio_consecutivo_format;
        }
        return '';
    }

    /**
     * Métodos
     */
    public function descargaLayout($id)
    {
        $subcontrato = Subcontrato::where('id_transaccion', $id)->first();
        $folio = str_pad($subcontrato->numero_folio, 5, 0, 0);
        return Excel::download(new EstimacionLayout($subcontrato), '#'.$folio.'.xlsx');
    }

    public function calculaAnticipo($id_antecedente, $partidas){
        $subcontrato = Subcontrato::find($id_antecedente);
        $anticipo_subc = $subcontrato->anticipo;
        if($anticipo_subc == 0){
            return 0;
        }

        $importe_anticipo = $subcontrato->anticipo_monto;
        $estimaciones = $subcontrato->estimaciones;
        foreach($estimaciones as $estimacion){
            if($estimacion->anticipo == 0){
                continue;
            }
            $imp_estimacion = $estimacion->sumaImportes;
            $anticipo_estimacion_monto = $imp_estimacion * $estimacion->anticipo / 100;
            $importe_anticipo -= $anticipo_estimacion_monto;
        }
        if($importe_anticipo <= 1){
            return 0;
        }
        $imp_items = array_sum(array_column($partidas, 'importe'));
        $imp_anticipo_esta_estimacion = $imp_items * $anticipo_subc / 100;
        if($importe_anticipo >= $imp_anticipo_esta_estimacion){
            return $subcontrato->anticipo;
        }else{
            return $importe_anticipo * 100 / $imp_items;
        }
    }
}
