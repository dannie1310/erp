<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\CADECO\Cambio;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
use App\Models\IGH\Usuario;
use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\PresupuestoContratista;
use Illuminate\Support\Facades\DB;

class AsignacionContratista extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.asignaciones';
    protected $primaryKey = 'id_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'registro',
        'fecha_hora_registro',
        'autorizo',
        'fecha_hora_autorizacion',
        'estado',
        'origen'
    ];

    /**
     * Relaciones
     */
    public function partidas(){
        return $this->hasMany(AsignacionContratistaPartida::class, 'id_asignacion', 'id_asignacion');
    }

    public function asignacionSubcontrato(){
        return $this->belongsTo(AsignacionSubcontrato::class, 'id_asignacion', 'id_asignacion');
    }

    public function contratoProyectado()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_transaccion', 'id_transaccion');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function usuarioAutorizo(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function presupuestosContratista()
    {
        return $this->hasManyThrough(PresupuestoContratista::class,AsignacionContratistaPartida::class, 'id_asignacion', 'id_transaccion','id_asignacion','id_transaccion')
            ->distinct();
    }

    public function conceptosContrato()
    {
        return $this->hasManyThrough(Contrato::class,AsignacionContratistaPartida::class, 'id_asignacion', 'id_concepto','id_asignacion','id_concepto')
            ->distinct();
    }

    public function asignacionEliminada()
    {
        return $this->belongsTo(AsignacionContratistaEliminada::class, 'id_asignacion');
    }

    public function subcontrato(){
        return $this->belongsTo(Subcontrato::class, 'id_transaccion', 'id_antecedente');
    }

    /**
     * Scopes
     */
    public function scopePendienteSubcontrato($query){
        return $query->whereDoesntHave('asignacionSubcontrato');
    }

    public function scopeProyectado($query)
    {
        return $query->has('contratoProyectado');
    }

    /**
     * Atributos
     */
    public function getFechaRegistroFormatAttribute(){
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:m");
    }

    public function getFechaAutorizoFormatAttribute(){
        $date = date_create($this->fecha_hora_autorizacion);
        return date_format($date,"d/m/Y H:m");
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->id_asignacion);
    }

    public function getUsuarioRegistroNombreAttribute()
    {
        return $this->usuarioRegistro->nombre_completo;
    }

    public function getEstadoFormatAttribute(){
        switch($this->estado){
            case 1:
                return 'Registrada';
            break;
            case 2:
                return 'Aplicada';
            break;
        }
    }


    public function getSumaTotalConDescuentoAttribute()
    {
        $suma_global = 0;
        $suma = 0;
        foreach ($this->contratoProyectado->presupuestos as $presupuesto)
        {
            foreach ($presupuesto->partidasAsignaciones->where('id_asignacion', $this->id_asignacion) as $asignacion)
            {
                $suma += $asignacion->importe_con_descuento;
            }

            $suma_global += $suma;
            $suma = 0;
        }
        return $suma_global;
    }

    public function getSumaSubtotalPartidasIvaAttribute()
    {
        return $this->suma_total_con_descuento * 0.16;
    }

    public function getSumaSubtotalPartidasTotalAttribute()
    {
        return $this->suma_total_con_descuento + $this->suma_subtotal_partidas_iva;
    }

    public function getMejorAsignadoAttribute()
    {
        $suma_mejor_asignado = 0;
        $valor_calculado = 0;
        $suma_mejor_por_partida = 0;
        $conceptos = $this->partidas()->groupBy('id_concepto')->pluck('id_concepto');
        foreach ($conceptos as $concepto) {
            $partida_asignacion = $this->partidas()->where('id_concepto', $concepto)->first();
            foreach ($this->contratoProyectado->presupuestos as $presupuesto) {
                $partida_encontrada = $presupuesto->partidas()->where('id_concepto', '=', $concepto)->first();
                if ($partida_encontrada) {
                    $valor_calculado = $partida_asignacion->suma_importes_con_descuento;

                    if ($suma_mejor_por_partida === 0) {
                        $suma_mejor_por_partida = $valor_calculado;
                    }
                    if ($valor_calculado < $suma_mejor_por_partida) {
                        $suma_mejor_por_partida = $valor_calculado;
                    }
                }
            }
            $suma_mejor_asignado = $suma_mejor_asignado + (float)$suma_mejor_por_partida;
            $suma_mejor_por_partida = 0;
        }
        return $suma_mejor_asignado;
    }

    public function getMejorAsignadoIvaAttribute()
    {
        return $this->mejor_asignado * 0.16;
    }

    public function getMejorAsignadoTotalAttribute()
    {
        return $this->mejor_asignado + $this->mejor_asignado_iva;
    }

    public function getDiferenciaAttribute()
    {
        return $this->suma_total_con_descuento - $this->mejor_asignado;
    }

    public function getDiferenciaIvaAttribute()
    {
        return $this->diferencia * 0.16;
    }

    public function getDiferenciaTotalAttribute()
    {
        return $this->diferencia + $this->diferencia_iva;
    }

    public function getMejoresOpcionesPorConceptoAttribute()
    {
        $array = [];
        $valor_calculado = 0;
        $suma_mejor_por_partida = 0;
        $id_presupuesto_optima = null;
        foreach ($this->contratoProyectado->conceptosSinOrden()->groupBy('id_concepto')->pluck('id_concepto') as $concepto) {
            $partida_asignacion = $this->partidas()->where('id_concepto', $concepto)->first();
            foreach ($this->contratoProyectado->presupuestos as $presupuesto) {
                $partida_encontrada = $presupuesto->partidas()->where('id_concepto', '=', $concepto)
                    ->where('precio_unitario', '!=', 0)
                    ->whereNotNull('precio_unitario')->first();
                if ($partida_encontrada && $partida_asignacion) {
                    $valor_calculado = $partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_unitario_compuesto;
                    if ($suma_mejor_por_partida === 0) {
                        $id_presupuesto_optima = $partida_encontrada->id_transaccion;
                        $suma_mejor_por_partida = $valor_calculado;
                    }
                    if ($valor_calculado < $suma_mejor_por_partida) {
                        $id_presupuesto_optima = $partida_encontrada->id_transaccion;
                        $suma_mejor_por_partida = $valor_calculado;
                    }
                }
            }
            if (!array_key_exists($concepto, $array)) {
                $array[$concepto] = $id_presupuesto_optima;
            }
            $valor_calculado = 0;
            $suma_mejor_por_partida = 0;
            $id_cotizacion_optima = null;
        }
        return $array;
    }

    public function getContratistasStrAttribute(){
        $razones_sociales = [];
        foreach($this->presupuestosContratista as $presupuesto){
            $razones_sociales [] = $presupuesto->empresa->razon_social;

        }
        return implode(" / ",$razones_sociales);
    }

    public function getAsignacionParcialAttribute(){
        if($this->contratoProyectado->conceptos->count() != $this->partidas->count()){
            return true;
        }
        foreach($this->partidas as $partida){
            if($partida->cantidad_asignada != $partida->contrato->cantidad_presupuestada){
                return true;
            }
        }
        return false;
    }

    /**
     * Métodos
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
            AsignacionContratistaPartidaEliminada::create([
                'id_partida_asignacion' => $partida->id_partida_asignacion,
                'id_transaccion' => $partida->id_transaccion,
                'id_asignacion' => $partida->id_asignacion,
                'id_concepto' => $partida->id_concepto,
                'cantidad_asignada' => $partida->cantidad_asignada,
                'cantidad_autorizada' => $partida->cantidad_autorizada,
            ]);
        }

        /**
         * Respaldar asignación
         */
        AsignacionContratistaEliminada::create([
            'id_asignacion' => $this->id_asignacion,
            'id_transaccion' => $this->id_transaccion,
            'registro' => $this->registro,
            'fecha_hora_registro' => $this->fecha_hora_registro,
            'autorizo' => $this->autorizo,
            'fecha_hora_autorizacion' => $this->fecha_hora_autorizacion,
            'estado' => $this->estado,
            'motivo_eliminacion' => $motivo,
        ]);
    }

    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    public function validarParaEliminar()
    {
        if ($this->estado != 1) {
            abort(400, "No se puede eliminar esta asignación porque se encuentra Autorizada.");
        }

        if ($this->asignacionSubcontrato != null) {
            abort(400, "No se puede eliminar esta asignacion porque cuenta con subcontrato.");
        }
    }

    public function tipo_cambio($tipo)
    {
        $tipo_cambio = Cambio::where('id_moneda','=', $tipo)->where('fecha', '=', $this->timestamp_registro)->first();
        return $tipo_cambio ? $tipo_cambio->cambio : $tipo_cambio = Cambio::where('id_moneda','=', $tipo)->orderByDesc('fecha')->first()->cambio;
    }

    /**
     * Datos para tabla comparativa de asignaciones (PDF)
     * @return array
     */
    public function datosComparativos()
    {
        $partidas = [];
        $presupuestos = [];


        foreach ($this->contratoProyectado->conceptos as $key => $item) {
            if (array_key_exists($item->id_concepto, $partidas)) {
                $partidas[$item->id_concepto]['cantidad'] = $partidas[$item->id_concepto]['cantidad'] + $item->cantidad_original;
            } else {
                $partidas[$item->id_concepto]['descripcion'] = $item->descripcion;
                $partidas[$item->id_concepto]['unidad'] = $item->unidad;
                $partidas[$item->id_concepto]['cantidad'] = $item->cantidad_original;
                $partidas[$item->id_concepto]['destino'] = $item->destino ? $item->destino->concepto->path_corta : 'no tiene';
            }
        }

        $tcUSD = Cambio::where('id_moneda','=', 2)->orderByDesc('fecha')->first()->cambio;
        $tcEUR = Cambio::where('id_moneda','=', 3)->orderByDesc('fecha')->first()->cambio;
        $tcLibra = Cambio::where('id_moneda','=', 4)->orderByDesc('fecha')->first()->cambio;

        $p = 0;
        foreach ($this->contratoProyectado->presupuestos()->asignado($this->id_asignacion)->get() as $pi => $presupuesto) {
            $presupuestos[$p]['id_transaccion'] = $presupuesto->id_transaccion;
            $presupuestos[$p]['empresa'] = $presupuesto->empresa->razon_social;
            $presupuestos[$p]['fecha'] = $presupuesto->fecha_guion_format;
            $presupuestos[$p]['vigencia'] = $presupuesto->DiasVigencia != 0 ? $presupuesto->DiasVigencia :'-';
            $presupuestos[$p]['anticipo'] = $presupuesto->anticipo != 0 ? $presupuesto->anticipo :'-';
            $presupuestos[$p]['dias_credito'] = $presupuesto->DiasCredito != 0 ? $presupuesto->DiasCredito : '-';
            $presupuestos[$p]['descuento_global'] = $presupuesto->PorcentajeDescuento != 0 ? $presupuesto->PorcentajeDescuento : '-';
            $presupuestos[$p]['descuento'] = $presupuesto->PorcentajeDescuento != 0 ? $this->numeroFormato($presupuesto->descuento) : '-';
            $presupuestos[$p]['suma_subtotal_partidas'] = $presupuesto->suma_subtotal_partidas;
            $presupuestos[$p]['suma_con_descuento'] = $presupuesto->subtotal_con_descuento;
            $presupuestos[$p]['iva_partidas'] = $presupuesto->iva_con_descuento;
            $presupuestos[$p]['total_partidas'] = $presupuesto->total_con_descuento;
            $presupuestos[$p]['observaciones'] = $presupuesto->observaciones;
            $presupuestos[$p]['tc_usd'] = number_format($tcUSD, 2, '.', ',');
            $presupuestos[$p]['tc_eur'] = number_format($tcEUR, 2, '.', ',');
            $presupuestos[$p]['tc_libra'] = number_format($tcLibra, 2, '.', ',');

            $partidas_asignadas = $this->partidas->where('id_transaccion', $presupuesto->id_transaccion);
            if(count($partidas_asignadas)>0) {
                $suma = 0;
                $suma_sin_descuento = 0;
                foreach ($partidas_asignadas as $asignada) {
                    $suma += $asignada->importe_con_descuento;
                    $suma_sin_descuento += $asignada->importe_calculado;
                }
                $descuento = $suma_sin_descuento - $suma;
                $presupuestos[$p]['asignacion_subtotal_partidas'] = $suma_sin_descuento;
                $presupuestos[$p]['asignacion_subtotal_partidas_descuento_global'] = $suma;
                $presupuestos[$p]['asignacion_descuento'] = $presupuesto->PorcentajeDescuento != 0 ? $this->numeroFormato($descuento) : '-';
                $presupuestos[$p]['asignacion_subtotal_descuento'] = $suma;
                $presupuestos[$p]['asignacion_iva'] = $suma * 0.16;
                $presupuestos[$p]['asignacion_total'] = $suma + ($suma * 0.16);
                $peso = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(1, $presupuesto->id_transaccion));
                $dolar = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(2, $presupuesto->id_transaccion));
                $euro = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(3, $presupuesto->id_transaccion));
                $libra = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(4, $presupuesto->id_transaccion));
                $presupuestos[$p]['subtotal_peso'] = $peso == 0 ? '-' : $this->monedaFormato($peso);
                $presupuestos[$p]['subtotal_dolar'] = $dolar == 0 ? '-' : $this->monedaFormato($dolar);
                $presupuestos[$p]['subtotal_euro'] = $euro == 0 ? '-' : $this->monedaFormato($euro);
                $presupuestos[$p]['subtotal_libra'] = $libra == 0 ? '-' : $this->monedaFormato($libra);
                $presupuestos[$p]['dolares'] = $dolar == 0 ? '-' : $this->numeroFormato($dolar/$tcUSD);
                $presupuestos[$p]['euros'] = $euro == 0 ? '-' : $this->numeroFormato($euro/$tcEUR);
                $presupuestos[$p]['libras'] = $libra == 0 ? '-' : $this->numeroFormato($libra/$tcLibra);
            }


            foreach ($presupuesto->partidas as $partida) {
                if (array_key_exists($partida->id_concepto, $partidas)) {
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['id_transaccion'] = $presupuesto->id_transaccion;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario'] = $partida->precio_unitario;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario_simple'] = $partida->precio_unitario_simple;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['id_moneda'] = $partida->IdMoneda;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_total_compuesto'] = $partida->precio_compuesto_total;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['importe_simple'] = $partida->importe_simple;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['importe_compuesto'] = $partida->importe_compuesto;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario_compuesto'] = $partida->precio_unitario_compuesto;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['tipo_cambio_descripcion'] = $partida->moneda ? $partida->moneda->abreviatura : '';
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['descuento_partida'] = $partida->PorcentajeDescuento;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['observaciones'] = $partida->Observaciones;
                    $partida_asignada = $this->partidas->where('id_concepto', $partida->id_concepto)->where('id_transaccion', $presupuesto->id_transaccion)->first();
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['cantidad_asignada'] = $partida_asignada ? number_format($partida_asignada->cantidad_autorizada, 2, '.', ',') : '-';
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['importe_asignado'] = $partida_asignada ? number_format($partida_asignada->importe_calculado, 2, '.', ',') : '-';
                }
            }
            $p++;
        }
        foreach ($this->contratoProyectado->presupuestos()->noAsignado($this->id_asignacion)->get() as $po => $presupuesto) {
            $presupuestos[$p]['id_transaccion'] = $presupuesto->id_transaccion;
            $presupuestos[$p]['empresa'] = $presupuesto->empresa->razon_social;
            $presupuestos[$p]['fecha'] = $presupuesto->fecha_guion_format;
            $presupuestos[$p]['vigencia'] = $presupuesto->DiasVigencia != 0 ? $presupuesto->DiasVigencia :'-';
            $presupuestos[$p]['anticipo'] = $presupuesto->anticipo != 0 ? $presupuesto->anticipo :'-';
            $presupuestos[$p]['dias_credito'] = $presupuesto->DiasCredito != 0 ? $presupuesto->DiasCredito : '-';
            $presupuestos[$p]['descuento_global'] = $presupuesto->PorcentajeDescuento != 0 ? $presupuesto->PorcentajeDescuento : '-';
            $presupuestos[$p]['descuento'] = $presupuesto->PorcentajeDescuento != 0 ? $this->numeroFormato($presupuesto->descuento) : '-';
            $presupuestos[$p]['suma_subtotal_partidas'] = $presupuesto->suma_subtotal_partidas;
            $presupuestos[$p]['suma_con_descuento'] = $presupuesto->subtotal_con_descuento;
            $presupuestos[$p]['iva_partidas'] = $presupuesto->iva_con_descuento;
            $presupuestos[$p]['total_partidas'] = $presupuesto->total_con_descuento;
            $presupuestos[$p]['observaciones'] = $presupuesto->observaciones;
            $presupuestos[$p]['tc_usd'] = number_format($tcUSD, 2, '.', ',');
            $presupuestos[$p]['tc_eur'] = number_format($tcEUR, 2, '.', ',');
            $presupuestos[$p]['tc_libra'] = number_format($tcLibra, 2, '.', ',');

            $partidas_asignadas = $this->partidas->where('id_transaccion', $presupuesto->id_transaccion);
            if(count($partidas_asignadas)>0) {
                $suma = 0;
                $suma_sin_descuento = 0;
                foreach ($partidas_asignadas as $asignada) {
                    $suma += $asignada->importe_con_descuento;
                    $suma_sin_descuento += $asignada->importe_calculado;
                }
                $descuento = $suma_sin_descuento - $suma;
                $presupuestos[$p]['asignacion_subtotal_partidas'] = $suma_sin_descuento;
                $presupuestos[$p]['asignacion_subtotal_partidas_descuento_global'] = $suma;
                $presupuestos[$p]['asignacion_descuento'] = $presupuesto->PorcentajeDescuento != 0 ? $this->numeroFormato($descuento) : '-';
                $presupuestos[$p]['asignacion_subtotal_descuento'] = $suma;
                $presupuestos[$p]['asignacion_iva'] = $suma * 0.16;
                $presupuestos[$p]['asignacion_total'] = $suma + ($suma * 0.16);
                $peso = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(1, $presupuesto->id_transaccion));
                $dolar = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(2, $presupuesto->id_transaccion));
                $euro = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(3, $presupuesto->id_transaccion));
                $libra = $this->sumaSubtotalTotalPorMoneda($this->sumaSubtotalPartidas(4, $presupuesto->id_transaccion));
                $presupuestos[$p]['subtotal_peso'] = $peso == 0 ? '-' : $this->monedaFormato($peso);
                $presupuestos[$p]['subtotal_dolar'] = $dolar == 0 ? '-' : $this->monedaFormato($dolar);
                $presupuestos[$p]['subtotal_euro'] = $euro == 0 ? '-' : $this->monedaFormato($euro);
                $presupuestos[$p]['subtotal_libra'] = $libra == 0 ? '-' : $this->monedaFormato($libra);
                $presupuestos[$p]['dolares'] = $dolar == 0 ? '-' : $this->numeroFormato($dolar/$tcUSD);
                $presupuestos[$p]['euros'] = $euro == 0 ? '-' : $this->numeroFormato($euro/$tcEUR);
                $presupuestos[$p]['libras'] = $libra == 0 ? '-' : $this->numeroFormato($libra/$tcLibra);
            }


            foreach ($presupuesto->partidas as $partida) {
                if (array_key_exists($partida->id_concepto, $partidas)) {
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['id_transaccion'] = $presupuesto->id_transaccion;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario'] = $partida->precio_unitario;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario_simple'] = $partida->precio_unitario_simple;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['id_moneda'] = $partida->IdMoneda;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_total_compuesto'] = $partida->precio_compuesto_total;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['importe_simple'] = $partida->importe_simple;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['importe_compuesto'] = $partida->importe_compuesto;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario_compuesto'] = $partida->precio_unitario_compuesto;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['tipo_cambio_descripcion'] = $partida->moneda ? $partida->moneda->abreviatura : '';
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['descuento_partida'] = $partida->PorcentajeDescuento;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['observaciones'] = $partida->Observaciones;
                    $partida_asignada = $this->partidas->where('id_concepto', $partida->id_concepto)->where('id_transaccion', $presupuesto->id_transaccion)->first();
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['cantidad_asignada'] = $partida_asignada ? number_format($partida_asignada->cantidad_autorizada, 2, '.', ',') : '-';
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['importe_asignado'] = $partida_asignada ? number_format($partida_asignada->importe_calculado, 2, '.', ',') : '-';
                }
            }
            $p++;
        }

        return [
            'presupuestos' => $presupuestos,
            'partidas' => $partidas
        ];
    }

    public function registrar($data)
    {
        try{
            DB::connection('cadeco')->beginTransaction();
            $origen = 0;
            if(array_key_exists('origen', $data)){
                $origen = $data['origen'];
            }
            $asignacion = AsignacionContratista::create([
                'id_transaccion' => $data['id_contrato'],  // contrato proyectado
                'estado' => 1,
                'origen' => $origen
            ]);
            $registradas = 0;
            foreach($data['presupuestos'] as $presupuesto){
                foreach($presupuesto['partidas'] as $partida){
                    if($partida && $partida['cantidad_asignada'] > 0){
                        AsignacionContratistaPartida::create([
                            'id_asignacion' => $asignacion->id_asignacion,
                            'id_transaccion' => $presupuesto['id_transaccion'],
                            'id_concepto' => $partida['id_concepto'],
                            'cantidad_asignada' => $partida['cantidad_asignada'],
                            'cantidad_autorizada' => $partida['cantidad_asignada'],
                            'justificacion' => $partida['justificacion'],
                        ]);
                        $registradas ++;
                    }
                }
            }

            if($registradas == 0){
                abort(403,'La asignación debe tener al menos una partida con cantidad asignada a un proveedor.');
            }

            DB::connection('cadeco')->commit();
            return $asignacion;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function generarSubcontratos()
    {
        $partidas = $this->partidas()->orderBy('id_transaccion')->get();
        $this->validarEmpresasAsignacion($partidas);
        try{
            DB::connection('cadeco')->beginTransaction();

            $subcontratos = [];
            foreach($partidas as $partida){
                $subcontrato = null;
                $id_moneda = $partida->presupuestoPartida->IdMoneda;
                if(array_key_exists($id_moneda , $subcontratos) && array_key_exists($partida->id_transaccion, $subcontratos[$id_moneda])){
                    $subcontrato = $subcontratos[$id_moneda][$partida->id_transaccion];
                }else{
                    $subcontratos[ $partida->presupuestoPartida->IdMoneda] = array();
                    $resp =
                        Subcontrato::Create([
                            'id_antecedente' => $this->id_transaccion,
                            'id_empresa' => $partida->presupuesto->id_empresa,
                            'id_sucursal' => $partida->presupuesto->id_sucursal,
                            'id_moneda' =>  $partida->presupuestoPartida->IdMoneda,
                            'PorcentajeDescuento' =>  $partida->presupuestoPartida->presupuesto->PorcentajeDescuento,
                            'anticipo' =>  $partida->presupuestoPartida->presupuesto->anticipo,
                            'observaciones' => $partida->presupuesto->observaciones,
                        ]);
                    $subcontratos[ $partida->presupuestoPartida->IdMoneda][$partida->id_transaccion] = $resp;
                    $subcontrato = $resp;
                    AsignacionSubcontrato::create([
                        'id_asignacion' => $this->id_asignacion,
                        'id_transaccion' => $resp->id_transaccion,
                    ]);
                }

                $partida_subc = ItemSubcontrato::create([
                    'id_transaccion' => $subcontrato->id_transaccion,
                    'id_antecedente' => $partida->id_transaccion,
                    'id_concepto' => $partida->id_concepto,
                    'cantidad' => $partida->cantidad_asignada,
                    'precio_unitario' => $partida->presupuestoPartida->precio_unitario_descuento_moneda_original, /*con descuento aplicado*/
                    'descuento' => $partida->presupuestoPartida->PorcentajeDescuento,
                    'cantidad_original1' => $partida->cantidad_asignada,
                    'precio_original1' => $partida->presupuestoPartida->precio_unitario_descuento_moneda_original,
                    'id_asignacion' => $partida->id_asignacion,
                ]);

                $importe = ($partida->presupuestoPartida->precio_unitario_descuento_moneda_original) * $partida->cantidad_asignada;
                $importe_descuento_general = $importe - ($importe * $subcontrato->PorcentajeDescuento/100);
                $tasa_iva = $partida->presupuestoPartida->presupuesto->tasa_iva;
                $subtotal = $importe_descuento_general;
                $impuesto = $subtotal  * ($tasa_iva / 100);
                $monto = $subtotal + $impuesto;

                $subcontrato->monto = $subcontrato->monto + $monto;
                $subcontrato->saldo = $subcontrato->saldo + $monto;
                $subcontrato->impuesto = $subcontrato->impuesto + $impuesto;

                $subcontrato->anticipo_monto =  (($subcontrato->monto - $subcontrato->impuesto) * $subcontrato->anticipo / 100) ;
                $subcontrato->anticipo_saldo =  (($subcontrato->saldo - $subcontrato->impuesto) * $subcontrato->anticipo / 100);

                $subcontrato->save();
            }
            $this->estado = 2;
            $this->save();
            DB::connection('cadeco')->commit();
            return $this;
        }catch(\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }

    }

    public function validarEmpresasAsignacion($partidas)
    {
        foreach($partidas as $partida) {
            $empresa = $partida->presupuesto->empresa;

            if($empresa && strlen(str_replace(" ","", $empresa->rfc))>0){
                $empresa->rfcValidaBoletinados($empresa->rfc);
                $empresa->rfcValidaEfos($empresa->rfc);
            }
        }
    }

    public function sumaSubtotalPartidas($tipo_moneda, $id_presupuesto)
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            if($tipo_moneda == $partida->presupuestoPartida->IdMoneda && $partida->presupuestoPartida->presupuesto->id_transaccion == $id_presupuesto)
            {
                $suma += $partida->importe_con_descuento;
            }
        }
        return $suma;
    }

    public function sumaSubtotalTotalPorMoneda($suma)
    {
        return $suma + ($suma * 0.16);
    }

    public function numeroFormato($numero)
    {
        return number_format($numero, 2, '.',',');
    }

    public function monedaFormato($numero)
    {
        return '$'.$this->numeroFormato($numero);
    }
}
