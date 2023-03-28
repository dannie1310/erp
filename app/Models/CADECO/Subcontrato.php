<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 03:55 PM
 */

namespace App\Models\CADECO;
use App\Models\CONTRATOS_LEGALES\Contratista;
use DateTime;
use DateTimeZone;
use App\Facades\Context;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
use App\Models\CADECO\Subcontratos\AsignacionSubcontratoEliminado;
use App\Models\CADECO\Subcontratos\ClasificacionSubcontrato;
use App\Models\CADECO\Subcontratos\SubcontratoEliminado;
use App\Models\CADECO\Subcontratos\SubcontratoPartidaEliminada;
use App\Models\CADECO\Sucursal;
use App\PDF\Contratos\SubcontratoFormato;
use App\Models\CADECO\Subcontratos\Subcontratos;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Collection;

class Subcontrato extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;
    public const TIPO = 51;
    public const OPCION = 2;
    public const OPCION_ANTECEDENTE = 1026;
    public const NOMBRE = "Subcontrato";
    public const ICONO = "fa fa-file-contract";

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'id_empresa',
        'id_sucursal',
        'id_moneda',
        'anticipo',
        'anticipo_monto',
        'anticipo_saldo',
        'monto',
        'PorcentajeDescuento',
        'impuesto',
        'impuesto_retenido',
        'id_costo',
        'retencion',
        'referencia',
        'observaciones',
        'id_usuario'
    ];
    protected $with = array('estimaciones');
    public $usuario_registra = 1;

    public $searchable = [
        'numero_folio',
        'referencia',
        'observaciones',
        'empresa.razon_social',
        'monto',
        'impuesto'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 51)
                ->where('opciones', '=', 2)
                ->whereIn('estado', [0, 1, 2])
                ->where(function ($q3) {
                    return $q3
                        ->whereHas('areasSubcontratantes', function ($q) {
                            return $q
                                ->whereHas('usuariosAreasSubcontratantes', function ($q2) {
                                    return $q2
                                        ->where('id_usuario', '=', auth()->id());
                                });
                        })
                        ->orHas('areasSubcontratantes', '=', 0);
                });
        });
    }

    /**
     * Relaciones Eloquent
     */

    public function areasSubcontratantes()
    {
        return $this->belongsToMany(TipoAreaSubcontratante::class, Context::getDatabase() . '.Contratos.cp_areas_subcontratantes', 'id_transaccion', 'id_area_subcontratante', 'id_antecedente');
    }

    public function contratoProyectado()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_antecedente', 'id_transaccion');
    }

    public function contratoProyectadoSinGlobalScope()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_antecedente', 'id_transaccion')->withoutGlobalScopes();
    }

    public function contratoProyectado_sgc()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_antecedente', 'id_transaccion')->withoutGlobalScopes();
    }

    public function clasificacionSubcontrato()
    {
        return $this->belongsTo(ClasificacionSubcontrato::class, 'id_transaccion');
    }

    public function estimaciones()
    {
        return $this->hasMany(Estimacion::class, 'id_antecedente', 'id_transaccion');
    }

    public function estimacionesSinGlobalScope()
    {
        return $this->hasMany(Estimacion::class, 'id_antecedente', 'id_transaccion')->withoutGlobalScopes();
    }

    public function subcontratos()
    {
        return $this->belongsTo(Subcontratos::class, 'id_transaccion');
    }

    public function costo()
    {
        return $this->belongsTo(Costo::class, 'id_costo');
    }

    public function partidas()
    {
        return $this->hasMany(ItemSubcontrato::class, 'id_transaccion');
    }

    public function fondo_garantia()
    {
        return $this->hasOne(FondoGarantia::class, 'id_subcontrato', 'id_transaccion');
    }

    public function moneda()
    {
        return $this->hasOne(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function facturas()
    {
        return $this->hasManyThrough(Factura::class,FacturaPartida::class,"id_antecedente","id_transaccion","id_transaccion","id_transaccion")
            ->distinct();
    }

    /*public function presupuestos()
    {
        return $this->hasManyThrough(PresupuestoContratista::class,ItemSubcontrato::class,"id_transaccion","id_transaccion","id_transaccion","id_antecedente")
            ->distinct();
    }*/

    public function pago_anticipado()
    {
        return $this->hasOne(SolicitudPagoAnticipado::class, 'id_antecedente', 'id_transaccion');
    }

    public function partidas_facturadas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_antecedente', 'id_transaccion');
    }

    public function subcontratoEliminado()
    {
        return $this->belongsTo(SubcontratoEliminado::class, 'id_transaccion');
    }

    public function asignacionSubcontrato()
    {
        return $this->belongsTo(AsignacionSubcontrato::class, 'id_transaccion', 'id_transaccion');
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(Transaccion::class, 'id_antecedente', 'id_transaccion');
    }

    public function presupuestosContratista()
    {
        return $this->hasMany(PresupuestoContratista::class, 'id_antecedente', 'id_antecedente');
    }

    public function solicitudesCambio()
    {
        return $this->hasMany(SolicitudCambioSubcontrato::class, 'id_antecedente', 'id_transaccion');
    }

    public function presupuesto(){
        return $this->hasOne(PresupuestoContratista::class, 'id_antecedente', 'id_antecedente')->where('id_empresa', '=', $this->id_empresa);
    }

    public function avance()
    {
        return $this->belongsTo(AvanceSubcontrato::class, 'id_transaccion', 'id_antecedente');
    }

    public function getAnticipoFormatAttribute()
    {
        return number_format(abs($this->anticipo), 2) . '%';
    }

    public function generaFondoGarantia()
    {
        if (is_null($this->fondo_garantia)) {
            if ($this->retencion > 0) {
                $fondo_garantia = new FondoGarantia();
                $fondo_garantia->id_subcontrato = $this->id_transaccion;
                $fondo_garantia->save();
                $this->refresh();
            } else {
                throw New \Exception('El subcontrato no tiene establecido un porcentaje de retención de fondo de garantía, el fondo de garantía no puede generarse');
            }
        }
    }

    public function partidasOrdenadas()
    {
        return $this->partidas()->leftJoin('dbo.contratos', 'contratos.id_concepto', 'items.id_concepto')
            ->where('items.id_transaccion', '=', $this->id_transaccion)
            ->orderBy('contratos.nivel', 'asc')->select('items.*', 'contratos.nivel');
    }

    public function getSubtotalAttribute()
    {
        return $this->monto - $this->impuesto + $this->impuesto_retenido;
    }

    public function getSubtotalAntesDescuentoAttribute()
    {
        return (($this->monto - $this->impuesto + $this->impuesto_retenido) * 100) / (100 - $this->PorcentajeDescuento);
    }

    public function scopeEstimable($query)
    {
        return $query->whereIn("estado", [0, 1]);
    }

    public function scopeSinFondo($query)
    {
        return $query->whereDoesntHave('fondo_garantia');
    }

    public function scopeConFondo($query)
    {
        return $query->whereHas('fondo_garantia');
    }

    public function scopeAvanceSubcontrato($query)
    {
        return $query->withoutGlobalScopes()->whereHas('contratoProyectado',function ($q){
            return $q->whereHas('contratoAreaSubcontratante', function ($q1) {
                return $q1->whereIn('id_area_subcontratante', [1, 2]);
            });
        })->where('tipo_transaccion', '=', 51)->where('opciones', '=', 2)->whereIn('estado', [0, 1]);
    }

    public function getNombre()
    {
        return 'SUBCONTRATO';
    }

    public function getMontoFacturadoEstimacionAttribute()
    {
        return round(FacturaPartida::query()->whereIn('id_antecedente', $this->estimaciones()->pluck('id_transaccion'))->sum('importe'));
    }

    public function getMontoFacturadoSubcontratoAttribute()
    {
        return round($this->partidas_facturadas()->sum('importe'), 2);
    }

    public function getMontoPagoAnticipadoAttribute()
    {
        return round($this->pago_anticipado()->where('estado', '>=', 0)->sum('monto'), 2);
    }

    public function getMontoDisponibleAttribute()
    {
        return round($this->saldo - ($this->montoFacturadoEstimacion + $this->montoFacturadoSubcontrato + $this->MontoPagoAnticipado), 2);
    }

    public function getTieneEstimacionesAttribute(){
        return count($this->estimaciones) > 0;
    }

    public function getTasaIvaAttribute()
    {
        if($this->impuesto == 0 || $this->subtotal == 0)
        {
            return 0;
        }else {
            return number_format((($this->impuesto / $this->subtotal) * 100), 0, '.', '');
        }
    }

    public function scopeSubcontratosDisponible($query, $id_empresa)
    {
        $transacciones = DB::connection('cadeco')->select(DB::raw("
                            select s.id_transaccion from transacciones s
                            left join (select SUM(monto) as solicitado, id_antecedente as id from  transacciones
                            where tipo_transaccion = 72 and opciones = 327681 and estado >= 0 and
                            id_obra = " . Context::getIdObra() . " group by id_antecedente)
                            as sol on sol.id = s.id_transaccion
                            left join
                            (select SUM(i.importe) as suma_anticipo, i.id_antecedente as id from items i
                            join transacciones factura on factura.id_transaccion = i.id_transaccion
                            join transacciones sub on sub.id_transaccion = i.id_antecedente
                            where factura.tipo_transaccion = 65 and factura.estado >= 0 and
                            sub.tipo_transaccion = 51 and sub.opciones = 2 and sub.estado >= 0 and sub.id_obra = " . Context::getIdObra() . "
                            group by i.id_antecedente)
                            as factura_anticipo on factura_anticipo.id = s.id_transaccion
                            left join (
                            select SUM(i.importe) as suma_e, e.id_antecedente as id  from items i
                            join transacciones f on f.id_transaccion = i.id_transaccion
                            join transacciones e on e.id_transaccion = i.id_antecedente
                            where f.tipo_transaccion = 65 and f.estado >= 0 and e.tipo_transaccion = 52 and e.estado >= 0 and f.id_obra =  " . Context::getIdObra() . "
                            group by e.id_antecedente )
                            as facturado_e on facturado_e.id = s.id_transaccion
                            where s.tipo_transaccion = 51 and s.estado >= 0 and  s.id_obra =  " . Context::getIdObra() . "  and s.opciones = 2
                            and (ROUND(s.saldo, 2) - ROUND((ISNULL(sol.solicitado,0) + ISNULL(factura_anticipo.suma_anticipo, 0) + ISNULL(facturado_e.suma_e, 0)),2)) > 1 and s.id_empresa=" . $id_empresa . "
                            order by s.id_transaccion;"));

        $transacciones = json_decode(json_encode($transacciones), true);

        return $query->whereIn('id_transaccion', $transacciones);
    }

    public function cambioEstadoEliminarEstimacion()
    {
        if ($this->estimaciones()->count('id_transaccion') == 0) {
            if ($this->estado == 1) {
                $this->estado = 0;
                $this->save();
            }
        }
    }

    /**
     * Este método implementa la lógica actualización de control de obra del procedimiento almacenado sp_aplicar_pagos
     * y se detona al registrar una orden de pago relacionada a una factura que ampara un subcontrato
     */
    public function actualizaControlObra(ItemFactura $item_factura, OrdenPago $orden_pago)
    {
        $importe = round($orden_pago->monto * -1 * $item_factura->proporcion_item, 2);
        if ($this->anticipo_monto > 0) {
            $factor = $importe / $this->anticipo_monto;
        } else {
            $factor = 0;
        }
        $estimaciones = $this->estimaciones;
        if ($estimaciones) {
            foreach ($estimaciones as $estimacion) {
                $movimientos = $estimacion->movimientos;
                foreach ($movimientos as $movimiento) {
                    $movimiento->monto_pagado = $movimiento->monto_pagado + round($movimiento->monto_total * $factor
                            * ((100 - $estimacion->retencion) / 100 - ($estimacion->monto - $estimacion->impuesto) / $estimacion->suma_importes)
                            , 2);
                    $movimiento->save();
                }
            }
        }
    }

    public function subcontratoParaEstimar($id_estimacion)
    {
        $respuesta = array();
        $items = array();
        $nivel_ancestros = '';

        foreach ($this->partidasOrdenadas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->ancestros as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            if($contrato == null)
            {
                $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("nivel", "=", $partida->nivel)->first();
                $partida = ItemSubcontrato::where('id_transaccion', '=',  $this->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
            }
            $items [$partida->nivel] = $partida->partidasEstimadas($id_estimacion, $this->id_antecedente, $contrato);
        }
        $respuesta = array(
            'folio' => $this->numero_folio_format,
            'referencia' => $this->referencia,
            'fecha_format' => $this->fecha_format,
            'partidas' => $items
        );
        return $respuesta;
    }

    public function getPartidasConvenioAttribute()
    {
        $items = array();
        $nivel_ancestros = '';

        foreach ($this->partidasOrdenadas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->ancestros as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            if($contrato == null)
            {
                $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("nivel", "=", $partida->nivel)->first();
                $partida = ItemSubcontrato::where('id_transaccion', '=',  $this->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
            }
            $items [$partida->nivel] = $partida->partidasEstimadas(null, $this->id_antecedente, $contrato);
        }
        return $items;
    }

    public function partidasPDF($id_estimacion)
    {
        $items = array();
        $nivel_ancestros = '';

        foreach ($this->partidasOrdenadas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->ancestros as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            if($contrato == null)
            {
                $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("nivel", "=", $partida->nivel)->first();
                $partida = ItemSubcontrato::where('id_transaccion', '=',  $this->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
            }
            $items [$partida->nivel] = $partida->partidasFormatoEstimacion($id_estimacion, $contrato);
        }

        return $items;
    }

    public function getFolioRevisionFormatAttribute()
    {
        return 'SUB ' . $this->numero_folio_format;
    }

    public function getMontoRevisionAttribute()
    {
        return number_format($this->anticipo_saldo, 2, ".", "");
    }

    public function getMontoRevisionFormatAttribute()
    {
        return '$ ' . number_format($this->monto_revision, 2, ".", ",");
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

    public function getImporteFondoGarantiaAttribute()
    {
        return ($this->monto - $this->impuesto) * $this->retencion / 100;
    }

    public function getPresupuestosAttribute()
    {
        /*NO SE USA RELACIÓN ELOQUENT PORQUE HAY CONFLICTOS CON LA SOBREESCRITURA DEL CAMPO id_transaccion*/
        $presupuestos_arr = [];
        foreach ($this->partidas as $item){
            $presupuestos_arr[] = $item->presupuesto;
        }
        $presupuestos =  collect($presupuestos_arr)->unique();
        return $presupuestos;
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
        $datos["tipo"] = Subcontrato::NOMBRE;
        $datos["tipo_numero"] = Subcontrato::TIPO;
        $datos["icono"] = Subcontrato::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #CONTRATOS PROYECTADOS
        $relaciones[$i] = $this->contratoProyectadoSinGlobalScope->datos_para_relacion;
        $i++;
        #PRESUPUESTOS
        $presupuestos = $this->presupuestos;
        foreach($presupuestos as $presupuesto)
        {
            try{
                $relaciones[$i] = $presupuesto->datos_para_relacion;
                $i++;
            } catch(\Exception $e)
            {}
        }
        #SUBCONTRATO
        $subcontrato = $this;

        $relaciones[$i] = $subcontrato->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
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
        #ESTIMACION
        foreach ($subcontrato->estimacionesSinGlobalScope as $estimacion){
            $relaciones[$i] = $estimacion->datos_para_relacion;
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
        }
        #SOLICITUD PAGO
        try{
            $relaciones[$i] = $this->pago_anticipado->datos_para_relacion;
            $i++;

        }catch (\Exception $e){

        }

        #PAGO DE SOLICITUD
        try{

            $relaciones[$i] = $this->pago_anticipado->pago->datos_para_relacion;
            $i++;
        }catch (\Exception $e){

        }

        #POLIZA DE PAGO DE SOLICITUD
        try{
            $relaciones[$i] = $this->pago_anticipado->pago->poliza->datos_para_relacion;
            $i++;
        }catch (\Exception $e){

        }

        #SOLICITUD DE CAMBIO
        foreach ($subcontrato->solicitudesCambio as $solicitud_cambio){
            $relaciones[$i] = $solicitud_cambio->datos_para_relacion;
            $i++;
        }

        $orden1 = array_column($relaciones, 'orden');
        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function getTieneNodoExtraordinarioAttribute()
    {
        $extra = $this->contratoProyectado_sgc->contratos()->agrupadorExtraordinario()->get();
        if(count($extra)>0){
            return true;
        } else{
            return false;
        }
    }

    public function getTieneNodoCambioPrecioAttribute()
    {
        $cp = $this->contratoProyectado_sgc->contratos()->agrupadorCambioPrecio()->get();
        if(count($cp)>0){
            return true;
        } else{
            return false;
        }
    }

    public function recalcula()
    {

        $saldo = 0;
        $subtotal = 0;
        foreach($this->partidas as $partida){
            $subtotal += $partida->cantidad * $partida->precio_unitario  - ($partida->cantidad * $partida->precio_unitario * $this->PorcentajeDescuento /100);
        }
        $monto = $subtotal * 1.16;
        $impuesto = $subtotal * 0.16;

        $diferencia_monto = $monto - $this->monto;

        $saldo = $this->saldo + $diferencia_monto;

        if($this->anticipo_monto > 0)
        {
            $nuevo_anticipo_porcentaje = $this->anticipo_monto / $subtotal * 100;
            $this->anticipo = $nuevo_anticipo_porcentaje;
        }

        $this->monto = $monto;
        $this->saldo = $saldo;
        $this->impuesto = $impuesto;

        $this->save();

    }

    public function updateContrato($data){
        try {
            DB::connection('cadeco')->beginTransaction();
            if($this->tiene_estimaciones){
                abort(403, "El subcontrato tiene estimaciones registradas.");
            }
            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_ini_ejec =New DateTime($data['fecha_ini_ejec']);
            $fecha_ini_ejec->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_fin_ejec =New DateTime($data['fecha_fin_ejec']);
            $fecha_fin_ejec->setTimezone(new DateTimeZone('America/Mexico_City'));
            if($fecha_ini_ejec > $fecha_fin_ejec){
                abort(403, 'La fecha de inicio de ejecución no puede ser posterior a la fecha de fin de ejección.');
            }

            $this->referencia = $data['referencia'];
            $this->fecha = $fecha->format("Y-m-d");
            $this->impuesto = $data['impuesto'];
            $this->impuesto_retenido = $data['retencion_iva'];
            $this->monto = $data['monto'];
            $this->saldo = $data['monto'];
            $this->retencion = $data['retencion_fg'];
            $data['id_costo']?$this->id_costo = $data['id_costo']:'';
            $this->save();

            if($this->subcontratos){
                $this->subcontratos->fecha_ini_ejec = $fecha_ini_ejec->format("Y-m-d "). date('H:i:s');
                $this->subcontratos->fecha_fin_ejec = $fecha_fin_ejec->format("Y-m-d "). date('H:i:s');
                $this->subcontratos->observacion = $data['observacion'];
                $this->subcontratos->save();
            }else{
                Subcontratos::create([
                    'id_transaccion' => $this->id_transaccion,
                    'id_clasificador' => 1,
                    'fecha_ini_ejec' => $fecha_ini_ejec->format("Y-m-d "). date('H:i:s'),
                    'fecha_fin_ejec' => $fecha_fin_ejec->format("Y-m-d "). date('H:i:s'),
                    'observacion' => $data['observacion'],
                ]);
            }

            if($this->clasificacionSubcontrato){
                $this->clasificacionSubcontrato->id_tipo_contrato = $data['id_tipo_contrato'];
                $this->clasificacionSubcontrato->actualizarFolio();
                $this->clasificacionSubcontrato->save();
            }else{
                ClasificacionSubcontrato::create([
                    'id_transaccion' => $this->id_transaccion,
                    'id_tipo_contrato' => $data['id_tipo_contrato']
                ]);
            }
            $contratista_legal = Contratista::where("rfc","=", $this->empresa->rfc)->first();
            if(!$contratista_legal)
            {
                Contratista::create([
                    "razon_social"=>$this->empresa->razon_social,
                    "idpersonalidad"=>$this->empresa->getIdPersonalidadRFC(),
                    "rfc" => $this->empresa->rfc,
                ]);
            } else {
                $contratista_legal->razon_social = $this->empresa->razon_social;
                $contratista_legal->idpersonalidad = $this->empresa->getIdPersonalidadRFC();
                $contratista_legal->save();
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar Subcontrato
     * @param $motivo
     * @throws \Exception
     */
    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->respaldar($motivo);
            foreach ($this->partidas()->get() as $partida) {
                $partida->delete();
            }
            $this->delete();
            DB::connection('cadeco')->commit();
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
        foreach ($this->partidas as $partida) {
            SubcontratoPartidaEliminada::create([
                'id_item' => $partida->id_item,
                'id_transaccion' => $partida->id_transaccion,
                'id_antecedente' => $partida->id_antecedente,
                'item_antecedente' => $partida->item_antecedente,
                'id_concepto' => $partida->id_concepto,
                'id_material' => $partida->id_material,
                'unidad' => $partida->unidad,
                'cantidad' => $partida->cantidad,
                'cantidad_material' => $partida->cantidad_material,
                'cantidad_mano_obra' => $partida->cantidad_mano_obra,
                'importe' => $partida->importe,
                'saldo' => $partida->saldo,
                'anticipo' => $partida->anticipo,
                'descuento' => $partida->descuento,
                'precio_unitario' => $partida->precio_unitario,
                'precio_material' => $partida->precio_material,
                'precio_mano_obra' => $partida->precio_mano_obra,
            ]);
        }

        /**
         * Respaldar subcontrato
         */
        SubcontratoEliminado::create([
            'id_transaccion' => $this->id_transaccion,
            'id_antecedente' => $this->id_antecedente,
            'id_referente' => $this->id_referente,
            'tipo_transaccion' => $this->tipo_transaccion,
            'numero_folio' => $this->numero_folio,
            'fecha' => $this->fecha,
            'estado' => $this->estado,
            'id_obra' => $this->id_obra,
            'id_empresa' => $this->id_empresa,
            'id_moneda' => $this->id_moneda,
            'opciones' => $this->opciones,
            'monto' => $this->monto,
            'saldo' => $this->saldo,
            'autorizado' => $this->autorizado,
            'impuesto' => $this->impuesto,
            'impuesto_retenido' => $this->impuesto_retenido,
            'diferencia' => $this->diferencia,
            'anticipo' => $this->anticipo,
            'anticipo_monto' => $this->anticipo_monto,
            'anticipo_saldo' => $this->anticipo_saldo,
            'PorcentajeDescuento' => $this->PorcentajeDescuento,
            'retencion' => $this->retencion,
            'observaciones' => $this->observaciones,
            'tipo_cambio' => $this->tipo_cambio,
            'comentario' => $this->comentario,
            'TipoLiberacion' => $this->TipoLiberacion,
            'FechaHoraRegistro' => $this->FechaHoraRegistro,
            'TcUSD' => $this->TcUSD,
            'TcEuro' => $this->TcEuro,
            'DiasCredito' => $this->DiasCredito,
            'DiasVigencia' => $this->DiasVigencia,
            'descuento' => $this->DiasVigencia,
            'porcentaje_anticipo_pactado' => $this->porcentaje_anticipo_pactado,
            'fecha_ejecucion' => $this->fecha_ejecucion,
            'fecha_contable' => $this->fecha_contable,
            'anticipo_pactado_monto' => $this->anticipo_pactado_monto,
            'id_usuario' => $this->id_usuario,
            'retencionIVA_2_3' => $this->retencionIVA_2_3,
        ]);

        /**
         * Respaldar Asignación Subcontrato
         */
        AsignacionSubcontratoEliminado::create([
            'id_asignacion' => $this->asignacionSubcontrato->id_asignacion,
            'id_transaccion' => $this->asignacionSubcontrato->id_transaccion,
            'motivo_eliminacion' => $motivo,
        ]);
    }

    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    public function validarParaEliminar()
    {
        if ($this->estado != 0) {
            abort(400, "No se puede eliminar este subcontrato porque se encuentra Autorizada.");
        }
        $mensaje = "";
        if($this->transaccionesRelacionadas()->count('id_transaccion') > 0)
        {
            foreach ($this->transaccionesRelacionadas()->get() as $antecedente)
            {
                $mensaje .= "-".$antecedente->tipo->Descripcion." #".$antecedente->numero_folio."\n";
            }
            abort(500, "Este subcontrato tiene la(s) siguiente(s) transaccion(es) relacionada(s): \n".$mensaje);
        }
    }

    /**
     * Cambiar estado de Presupuesto y Contrato Proyectado
     */
    public function cambiaEstados()
    {
        foreach ($this->presupuestos as $presupuesto)
        {
            if($presupuesto->estado == 2)
            {
                $presupuesto->update([
                    'estado' => 1
                ]);
            }
        }

        if($this->contratoProyectado->estado == 1)
        {
            $this->contratoProyectado->update([
                'estado' => 0
            ]);
        }
    }

    public function pdf(){
        $pdf = new SubcontratoFormato($this);
        return $pdf->create();
    }

    public function getMontoActualizacionesAplicadas($id_solicitud = null)
    {
        if($id_solicitud>0){
            return $this->solicitudesCambio()->aplicadas()->where("id_transaccion","<",$id_solicitud)->sum("monto");

        } else {
            return $this->solicitudesCambio()->aplicadas()->sum("monto");
        }

    }

    public function getMontoActualizacionesAplicadasFormat($id_solicitud = null)
    {
        return "$".number_format($this->getMontoActualizacionesAplicadas($id_solicitud),2,".",",");

    }

    public function getImpuestoActualizacionesAplicadas($id_solicitud = null)
    {
        if($id_solicitud>0){
            return $this->solicitudesCambio()->aplicadas()->where("id_transaccion","<",$id_solicitud)->sum("impuesto");

        } else {
            return $this->solicitudesCambio()->aplicadas()->sum("impuesto");
        }

    }

    public function getImpuestoActualizacionesAplicadasFormat($id_solicitud = null)
    {
        return "$".number_format($this->getImpuestoActualizacionesAplicadas($id_solicitud),2,".",",");

    }

    public function getSubtotalActualizacionesAplicadas($id_solicitud = null)
    {
        return $this->getMontoActualizacionesAplicadas($id_solicitud)-$this->getImpuestoActualizacionesAplicadas($id_solicitud);

    }

    public function getSubtotalActualizacionesAplicadasFormat($id_solicitud = null)
    {
        return "$".number_format($this->getSubtotalActualizacionesAplicadas($id_solicitud),2,".",",");

    }

    public function getMontoInicial()
    {
        return $this->monto-$this->solicitudesCambio()->aplicadas()->sum("monto");
    }

    public function getMontoInicialFormat($id_solicitud = null)
    {
        return "$".number_format($this->getMontoInicial(),2,".",",");
    }

    public function getImpuestoInicial()
    {
        return $this->impuesto-$this->solicitudesCambio()->aplicadas()->sum("impuesto");
    }

    public function getImpuestoInicialFormat()
    {
        return "$".number_format($this->getImpuestoInicial(),2,".",",");
    }

    public function getSubtotalInicial()
    {
        return $this->getMontoInicial()-$this->getImpuestoInicial();
    }

    public function getSubtotalInicialFormat()
    {
        return "$".number_format($this->getSubtotalInicial(),2,".",",");
    }

    public function getPorcentajeCambio($id_solicitud)
    {
        return $this->getMontoActualizacionesAplicadas($id_solicitud) *100 /$this->getMontoInicial();
    }

    public function getPorcentajeCambioFormat($id_solicitud)
    {
        return number_format($this->getPorcentajeCambio($id_solicitud),4,"." ,","). "%";
    }

    public function subcontratoParaAvance($avance)
    {
        $respuesta = array();
        $items = array();
        $nivel_ancestros = '';

        foreach ($this->partidasOrdenadas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->ancestros as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            if($contrato == null)
            {
                $contrato = Contrato::where('id_transaccion', '=', $this->id_antecedente)->where("nivel", "=", $partida->nivel)->first();
                $partida = ItemSubcontrato::where('id_transaccion', '=',  $this->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
            }
            $items [$partida->nivel] = $partida->partidasAvanceSubcontrato($avance);
        }
        $respuesta = array(
            'folio' => $this->numero_folio_format,
            'referencia' => $this->referencia,
            'fecha_format' => $this->fecha_format,
            'partidas' => $items
        );
        return $respuesta;
    }
}
