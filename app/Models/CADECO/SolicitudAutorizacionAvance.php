<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\ItemContratista;
use App\Models\CADECO\Estimaciones\ItemSolicitudAutorizacionAvanceEliminada;
use App\Models\CADECO\Estimaciones\SolicitudAutorizacionAvanceEliminada;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Penalizacion;
use App\Models\CADECO\SubcontratosEstimaciones\PenalizacionLiberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;
use App\Models\CADECO\SubcontratosEstimaciones\Estimacion;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;

class SolicitudAutorizacionAvance extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;
    public const TIPO = 55;
    public const OPCION = 0;
    public const NOMBRE = "Solicitud de Autorización de Avance de Estimación";
    public const ICONO = "fa fa-building";

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'saldo',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        'id_usuario',
        'retencion',
        'id_empresa',
        'id_moneda',
        'numero_folio',
        'IVARetenido'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', self::TIPO)
                ->where('opciones', self::OPCION);
        });
    }

    /**
     * Relaciones
     */
    public function fondoGarantia()
    {
        return $this->belongsTo(FondoGarantia::class,  'id_antecedente','id_subcontrato')->withoutGlobalScopes();
    }

    public function subcontratoEstimacion()
    {
        return $this->hasOne(Estimacion::class, 'IDEstimacion', 'id_transaccion');
    }

    public function subcontrato()
    {
        return $this->belongsTo(Transaccion::class, 'id_antecedente','id_transaccion')->withoutGlobalScopes()->where('tipo_transaccion', '=', 51)->where('opciones', '=', 2);
    }

    public function items()
    {
        return $this->hasMany(ItemSolicitudAutorizacionAvance::class, 'id_transaccion', 'id_transaccion');
    }

    public function retenciones()
    {
        return $this->hasMany(Retencion::class, 'id_transaccion', 'id_transaccion');
    }

    public function liberaciones()
    {
        return $this->hasMany(Liberacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'id_transaccion', 'id_transaccion');
    }

    public function penalizaciones()
    {
        return $this->hasMany(Penalizacion::class, 'id_transaccion');
    }

    public function penalizacionLiberaciones()
    {
        return $this->hasMany(PenalizacionLiberacion::class, 'id_transaccion');
    }

    public function solicitudEliminada()
    {
        return $this->belongsTo(SolicitudAutorizacionAvanceEliminada::class, 'id_transaccion','id_transaccion');
    }

    public function itemsXContratistas()
    {
        return $this->hasMany(ItemContratista::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Scope
     */
    public function scopeProveedor($query, $id_obra)
    {
        $empresas = Empresa::where('rfc', auth()->user()->usuario)->pluck('id_empresa');
        return $query->withoutGlobalScopes()->whereIn('id_empresa', $empresas)->where('id_obra', $id_obra)->where('tipo_transaccion', '=', 55)->whereIn("estado", [0, 1])->orderby('id_transaccion','desc');
    }

    /**
     * Atributos
     */
    public function getSubtotalAttribute()
    {
        return $this->monto - $this->impuesto;
    }

    public function getSubtotalFormatAttribute()
    {
        return '$ ' . number_format($this->subtotal, 2);
    }

    public function getSumaImportesAttribute()
    {
        return $this->items->sum('importe');
    }

    public function getSumaImportesFormatAttribute()
    {
        return '$ ' . number_format($this->suma_importes, 2, ".", ",");
    }

    public function getMontoAnticipoAplicadoAttribute()
    {
        return $this->suma_importes * (($this->anticipo) / 100);
    }

    public function getMontoAnticipoAplicadoFormatAttribute()
    {
        return '$ ' . number_format($this->monto_anticipo_aplicado, 2);
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
        if ($this->subcontrato->impuesto != 0)
        {
            return $this->subtotal_orden_pago * 0.16;
        } else {
            return 0;
        }
    }

    public function getIvaOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->iva_orden_pago, 2);
    }

    public function getTotalOrdenPagoAttribute()
    {
        $total = ($this->subtotal_orden_pago + $this->iva_orden_pago) - $this->IVARetenido;
        return $total;
    }

    public function getTotalOrdenPagoFormatAttribute()
    {
        return '$ ' . number_format($this->total_orden_pago, 2);
    }

    public function getAnticipoALiberarAttribute()
    {
        return $this->subcontratoEstimacion ? $this->subcontratoEstimacion->ImporteAnticipoLiberar : 0;
    }

    public function getAnticipoALiberarFormatAttribute()
    {
        return '$ ' . number_format($this->anticipo_a_liberar, 2);
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

    public function getMontoAPagarAttribute()
    {
        $monto_pagar = $this->total_orden_pago + $this->anticipo_a_liberar;
        if ($this->configuracion->retenciones_antes_iva == 0) {
            $monto_pagar -= $this->retenciones->sum("importe");
            $monto_pagar -= $this->IVARetenido;
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

    public function getAnticipoAnteriorProveedorAttribute()
    {
        $anticipo = 0;
        $estimaciones_anteriores = $this->withoutGlobalScopes()->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();

        foreach($estimaciones_anteriores as $estimacion){
            $anticipo += $estimacion->monto_anticipo_aplicado;
        }
        return $anticipo;
    }

    public function getIvaRetenidoCalculadoAnteriorProveedorAttribute()
    {
        $iva_retenido = 0;
        $estimaciones_anteriores = $this->withoutGlobalScopes()->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();

        foreach($estimaciones_anteriores as $estimacion){
            $iva_retenido += $estimacion->iva_retenido_calculado;
        }
        return $iva_retenido;
    }

    public function getAcumuladoPenalizacionesAnterioresProveedorAttribute()
    {
        $acumulado = 0;
        $estimaciones_anteriores = $this->withoutGlobalScopes()->where('id_antecedente', '=', $this->id_antecedente)
            ->where('numero_folio', '<', $this->numero_folio)
            ->where('estado', '>=', 0)->get();

        foreach ($estimaciones_anteriores as $estimacion) {
            $acumulado += $estimacion->suma_penalizaciones;
        }
        return $acumulado;
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

    public function getIvaRetenidoCalculadoAttribute()
    {
        return $this->IVARetenido + $this->retencionIVA_2_3;
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

    public function getRetencionIva23FormatAttribute()
    {
        return '$ ' . number_format($this->retencionIVA_2_3, 2);
    }

    /**
     * Métodos
     */
    public function solicitudes()
    {
        $datos = [];
        $i = 0;
        $configuracion_obra = ConfiguracionObra::withoutGlobalScopes()->where('tipo_obra','!=',2)->get();
        foreach ($configuracion_obra as $proyecto)
        {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $proyecto->proyecto->base_datos);
            $datos_estimacion= self::proveedor($proyecto->id_obra)->get();
            foreach($datos_estimacion as $key => $estimacion){
                $datos['data'][$i]['id'] = $estimacion->getKey();
                $datos['data'][$i]['numero_folio_format'] = $estimacion->numero_folio_format;
                $datos['data'][$i]['estado'] = (int) $estimacion->estado;
                $datos['data'][$i]['fecha'] = $estimacion->fecha_format;
                $datos['data'][$i]['monto_format'] = $estimacion->monto_format;
                $datos['data'][$i]['numero_folio_sub'] = $estimacion->subcontrato->numero_folio_format;
                $datos['data'][$i]['referencia_sub'] = $estimacion->subcontrato->referencia;
                $datos['data'][$i]['contratista'] = $estimacion->subcontrato->empresa->razon_social;
                $datos['data'][$i]['proyecto'] = $proyecto->nombre;
                $datos['data'][$i]['base'] = $proyecto->proyecto->base_datos;
                $i++;
            }
        }
        if($i == 0)
        {
            $datos['data'] = [];
        }
        $datos['meta']['pagination']['count'] = $i;
        $datos['meta']['pagination']['current_page'] = 1;
        $datos['meta']['pagination']['per_page'] = 1;
        $datos['meta']['pagination']['total'] = $i;
        $datos['meta']['pagination']['total_pages'] = 1;
        return $datos;
    }

    public function registrar($data)
    {
        try {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $data['base']);
            $subcontrato = Transaccion::withoutGlobalScopes()->where('id_transaccion', $data['id_antecedente'])->where('tipo_transaccion', '=', 51)->first();
            $data['id_obra'] = $subcontrato->id_obra;
            $data['id_empresa'] = $subcontrato->id_empresa;
            $data['id_moneda'] = $subcontrato->id_moneda;
            $data['retencion'] = $subcontrato->retencion;
            $data['anticipo'] = $subcontrato->anticipo;
            $data['numero_folio'] = $this->calcularFolio($subcontrato->id_obra);
            $solicitud = $this->create($data);
            $solicitud->estimaConceptos($data['conceptos']);
            $solicitud->recalculaDatosGenerales();
            DB::connection('cadeco')->commit();
            return $solicitud;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function calcularFolio($id_obra)
    {
        $est = Transaccion::withoutGlobalScopes()->where('tipo_transaccion', '=', 55)->where('id_obra','=', $id_obra)->orderBy('numero_folio', 'DESC')->first();
        return $est ? $est->numero_folio + 1 : 1;
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

    public function recalculaDatosGenerales()
    {
        $this->refresh();

        $this->monto = $this->monto_a_pagar;
        $this->saldo = $this->monto_a_pagar;
        $this->impuesto = $this->iva_orden_pago;
        $this->save();

        /*
        $this->subcontratoEstimacion->PorcentajeFondoGarantia = ($this->retencion);
        $this->subcontratoEstimacion->ImporteFondoGarantia = $this->retencion_fondo_garantia_orden_pago;
        $this->subcontratoEstimacion->save();
        */
    }

    public function generaFondoGarantia()
    {
        if (is_null($this->fondoGarantia)) {
            if ($this->retencion > 0) {
                $this->fondoGarantia()->create([
                    'id_subcontrato' => $this->id_antecedente,

                ]);
                $this->refresh();
            } else {
                throw New \Exception('El subcontrato no tiene establecido un porcentaje de retención de fondo de garantía, el fondo de garantía no puede generarse');
            }
        }
    }

    /**
     * Obtener estimación con las partidas ordenadas dependiendo los niveles de los contratos.
     * para el portal de proveedores
     */
    public function subcontratoAEstimar($base)
    {
        return[
            'fecha_inicial' => $this->cumplimiento_form,
            'fecha_final' => $this->vencimiento_form,
            'fecha_cumplimiento' => $this->cumplimiento,
            'fecha_vencimiento' => $this->vencimiento,
            'fecha' => $this->fecha_format,
            'razon_social' => $this->empresa->razon_social,
            'moneda' => $this->moneda->nombre,
            'observaciones' => $this->observaciones,
            'folio' => $this->numero_folio_format,
            'subtotal' => $this->subtotal_orden_pago_format,
            'iva' => $this->iva_orden_pago_format,
            'total' => $this->total_orden_pago_format,
            'id_empresa' => $this->empresa->id_empresa,
            'anticipo_format' => $this->anticipo_format,
            'monto_anticipo_aplicado' => $this->monto_anticipo_aplicado,
            'estado' => $this->estado,
            'estado_format' => $this->estado_descripcion,
            'folio_subcontrato' => $this->subcontrato->numero_folio_format,
            'referencia' => $this->subcontrato->referencia,
            'subcontrato' => $this->paraEstimar($this->id_antecedente,$base,$this->id_transaccion),
            'id_obra' => $this->id_obra,
            'suma_importes' => $this->suma_importes_format,
            'anticipo' => $this->anticipo,
            'monto_anticipo_aplicado_format' => $this->monto_anticipo_aplicado_format,
            'retencion' => $this->retencion,
            'retencion_fondo_garantia' => $this->retencion_fondo_garantia_orden_pago_format,
            'total_retenciones' => $this->suma_retenciones_format,
            'total_retencion_liberadas' => $this->suma_liberaciones_format,
            'total_deductivas' => $this->suma_deductivas_format,
            'suma_penalizaciones' => $this->suma_penalizaciones_format,
            'suma_penalizaciones_liberadas' => $this->suma_penalizaciones_liberadas_format,
            'subtotal_orden_pago' => $this->subtotal_orden_pago_format,
            'iva_orden_pago' => $this->iva_orden_pago_format,
            'retencion_iva_porcentaje' => $this->iva_retenido_porcentaje,
            'retencion_iva_format' => $this->iva_retenido_format,
            'total_orden_pago' => $this->total_orden_pago_format,
            'total_anticipo_liberar' => $this->anticipo_a_liberar_format,
            'monto_pagar_format' => $this->monto_a_pagar_format,
            'retencion_iva' => $this->IVARetenido,
            'retencion_iva4_format' => $this->retencion_iva4_format,
            'retencion_iva4' => $this->retencion_iva4,
            'retencion_iva6_format' => $this->retencion_iva6_format,
            'retencion_iva6' => $this->retencion_iva6,
            'retencion_iva23' => $this->retencionIVA_2_3,
            'retencion_iva23_format' => $this->retencion_iva23_format
        ];
    }

    public function paraEstimar($id,$base,$id_estimacion)
    {
        $items = array();
        $nivel_ancestros = '';
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $subcontrato = $this->findSubcontrato($id,$base);
        $partidas = ItemSubcontrato::leftJoin('dbo.contratos', 'contratos.id_concepto', 'items.id_concepto')
            ->where('items.id_transaccion', '=', $id)
            ->orderBy('contratos.nivel', 'asc')->select('items.*', 'contratos.nivel')->get();
        foreach ($partidas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->getAncestrosSinContextoAttribute($subcontrato->id_antecedente,$base) as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::withoutGlobalScopes()->where('id_transaccion', '=', $subcontrato->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            if($contrato == null)
            {
                $contrato = Contrato::withoutGlobalScopes()->where('id_transaccion', '=', $subcontrato->id_antecedente)->where("nivel", "=", $partida->nivel)->first();
                $partida = ItemSubcontrato::withoutGlobalScopes()->where('id_transaccion', '=',  $subcontrato->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
            }
            $items [$partida->nivel] = $partida->partidasEstimadasSinContexto($id_estimacion, $subcontrato->id_antecedente, $contrato, $base);
        }
        return array(
            'folio' => $subcontrato->numero_folio_format,
            'referencia' => $subcontrato->referencia,
            'fecha_format' => $subcontrato->fecha_format,
            'partidas' => $items
        );
    }

    private function findSubcontrato($id, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return Transaccion::withoutGlobalScopes()->where('id_transaccion', $id)->where('tipo_transaccion', '=', 51)->first();
    }

    public function editar($datos)
    {
        try {
            $fecha_inicial = New DateTime($datos['fecha_cumplimiento']);
            $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_final = New DateTime($datos['fecha_vencimiento']);
            $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
            DB::connection('cadeco')->beginTransaction();

            foreach ($datos['subcontrato']['partidas'] as $partida) {

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
                    if ($partida['id_item_estimacion'] == 0 && $partida['cantidad_estimacion'] != 0) {
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

    public function eliminar($base,$motivo)
    {
        try {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $base);
            DB::connection('cadeco')->beginTransaction();
            $this->respaldar($motivo);
            foreach ($this->items()->get() as $item) {
                $item->delete();
            }
            $this->delete();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function respaldar($motivo)
    {
        /**
         * Respaldar partidas
         */
        foreach ($this->items as $partida) {
            ItemSolicitudAutorizacionAvanceEliminada::create([
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
        SolicitudAutorizacionAvanceEliminada::create([
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
            'saldo' => $this->saldo ? $this->saldo : 0,
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
            'fecha_eliminacion' => date('Y-m-d H:i:s'),
            'usuario_elimina' => auth()->id()
        ]);
    }

    /**
     * Reglas de negocio que debe cumplir la solicitud a eliminar
     */
    public function validarParaEliminar()
    {
        if ($this->estado != 0) {
            abort(400, "No se puede eliminar está solicitud porque se encuentra Autorizada.");
        }
    }

    public function getPartidasPDFProveedor($base){
        $subcontrato = $this->subcontrato;
        $items = array();
        $nivel_ancestros = '';

        $partidasOrdenadas = ItemSubcontrato::Where('items.id_transaccion', '=', $subcontrato->id_transaccion)->leftJoin('dbo.contratos', 'contratos.id_concepto', 'items.id_concepto')
            ->where('items.id_transaccion', '=', $subcontrato->id_transaccion)
            ->orderBy('contratos.nivel', 'asc')->select('items.*', 'contratos.nivel')->get();

        foreach ($partidasOrdenadas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->getAncestrosSinContextoAttribute($subcontrato->id_antecedente,$base) as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::where('id_transaccion', '=', $subcontrato->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            if($contrato == null)
            {
                $contrato = Contrato::where('id_transaccion', '=', $subcontrato->id_antecedente)->where("nivel", "=", $partida->nivel)->first();
                $partida = ItemSubcontrato::where('id_transaccion', '=',  $subcontrato->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
            }
            $items [$partida->nivel] = $partida->partidasFormatoEstimacion($this->id_transaccion, $contrato);
        }
        return $items;
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

    public function registrarIVARetenido($retenciones)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $retenciones['base']);
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
}
