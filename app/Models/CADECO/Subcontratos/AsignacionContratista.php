<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\CADECO\Cambio;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
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
        'estado'
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

    public function presupuestoContratista()
    {
        return $this->belongsTo(PresupuestoContratista::class, 'id_transaccion', 'id_antecedente');
    }

    public function asignacionEliminada()
    {
        return $this->belongsTo(AsignacionContratistaEliminada::class, 'id_asignacion');
    }

    /**
     * Scopes
     */
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
                $suma += $asignacion->importe_calculado;
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
        $dolar = $this->tipo_cambio(2);
        $euro = $this->tipo_cambio(3);
        $libra = $this->tipo_cambio(4);
        $conceptos = $this->partidas()->groupBy('id_concepto')->pluck('id_concepto');
        foreach ($conceptos as $concepto) {
            $partida_asignacion = $this->partidas()->where('id_concepto', $concepto)->first();
            foreach ($this->contratoProyectado->presupuestos as $presupuesto) {
                $partida_encontrada = $presupuesto->partidas()->where('id_concepto', '=', $concepto)->first();
                if ($partida_encontrada) {
                    switch ($partida_encontrada->IdMoneda) {
                        case (1):
                            $valor_calculado = $partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_unitario;
                            break;
                        case (2):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_unitario * $dolar);
                            break;
                        case (3):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_unitario * $euro);
                            break;
                        case (4):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_unitario * $libra);
                            break;
                    }

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
        $precios = [];


        foreach ($this->contratoProyectado->conceptos as $key => $item) {
            if (array_key_exists($item->id_concepto, $partidas)) {
                $partidas[$item->id_concepto]['cantidad'] = $partidas[$item->id_concepto]['cantidad'] + $item->cantidad_original;
            } else {
                $partidas[$item->id_concepto]['descripcion'] = $item->descripcion;
                $partidas[$item->id_concepto]['unidad'] = $item->unidad;
                $partidas[$item->id_concepto]['cantidad'] = $item->cantidad_original;
                $partidas[$item->id_concepto]['destino'] = $item->destino ? $item->destino->ruta : '';
            }
        }

        foreach ($this->contratoProyectado->presupuestos as $p => $presupuesto) {
            $presupuestos[$p]['id_transaccion'] = $presupuesto->id_transaccion;
            $presupuestos[$p]['empresa'] = $presupuesto->empresa->razon_social;
            $presupuestos[$p]['fecha'] = $presupuesto->fecha_guion_format;
            $presupuestos[$p]['vigencia'] = $presupuesto->DiasVigencia != 0 ? $presupuesto->DiasVigencia :'-';
            $presupuestos[$p]['anticipo'] = $presupuesto->anticipo != 0 ? $presupuesto->anticipo :'-';
            $presupuestos[$p]['dias_credito'] = $presupuesto->DiasCredito != 0 ? $presupuesto->DiasCredito : '-';
            $presupuestos[$p]['descuento_global'] = $presupuesto->PorcentajeDescuento != 0 ? $presupuesto->PorcentajeDescuento : '-';
            $presupuestos[$p]['descuento'] = $presupuesto->PorcentajeDescuento != 0 ? $presupuesto->descuento : '-';
            $presupuestos[$p]['suma_subtotal_partidas'] = $presupuesto->suma_subtotal_partidas;
            $presupuestos[$p]['suma_con_descuento'] = $presupuesto->subtotal_con_descuento;
            $presupuestos[$p]['iva_partidas'] = $presupuesto->iva_con_descuento;
            $presupuestos[$p]['total_partidas'] = $presupuesto->total_con_descuento;
            $presupuestos[$p]['observaciones'] = $presupuesto->observaciones;
            $presupuestos[$p]['tc_usd'] = number_format($presupuesto->dolar, 2, '.', ',');
            $presupuestos[$p]['tc_eur'] = number_format($presupuesto->euro, 2, '.', ',');
            $presupuestos[$p]['tc_libra'] = number_format($presupuesto->libra, 2, '.', ',');


            $partidas_asignadas = $this->partidas->where('id_transaccion', $presupuesto->id_transaccion);
            if(count($partidas_asignadas)>0) {
                $suma = 0;
                foreach ($partidas_asignadas as $asignada) {
                    $suma += $asignada->importe_calculado;
                }
                $descuento = ($suma * $presupuesto->PorcentajeDescuento)/100;
                $subtotal_descuento = $suma - $descuento;
                $presupuestos[$p]['asignacion_subtotal_partidas'] = $suma;
                $presupuestos[$p]['asignacion_descuento'] = $presupuesto->PorcentajeDescuento != 0 ? $descuento : '-';
                $presupuestos[$p]['asignacion_subtotal_descuento'] = $subtotal_descuento;
                $presupuestos[$p]['asignacion_iva'] = $subtotal_descuento * 0.16;
                $presupuestos[$p]['asignacion_total'] = $subtotal_descuento + ($subtotal_descuento * 0.16);
                $peso = $this->sumaSubtotalPartidas(1);
                $dolar = $this->sumaSubtotalPartidas(2);
                $euro = $this->sumaSubtotalPartidas(3);
                $libra = $this->sumaSubtotalPartidas(4);
                $presupuestos[$p]['subtotal_peso'] = $peso == 0 ? '-' : $this->numeroFormato($this->sumaSubtotalTotalPorMoneda($peso));
                $presupuestos[$p]['subtotal_dolar'] = $dolar == 0 ? '-' : $this->numeroFormato($this->sumaSubtotalTotalPorMoneda($dolar));
                $presupuestos[$p]['subtotal_euro'] = $euro == 0 ? '-' : $this->numeroFormato($this->sumaSubtotalTotalPorMoneda($euro));
                $presupuestos[$p]['subtotal_libra'] = $libra == 0 ? '-' : $this->numeroFormato($this->sumaSubtotalTotalPorMoneda($libra));
                $presupuestos[$p]['dolares'] = $dolar == 0 ? '-' : $this->numeroFormato($dolar/$presupuesto->dolar);
                $presupuestos[$p]['euros'] = $euro == 0 ? '-' : $this->numeroFormato($euro/$presupuesto->euro);
                $presupuestos[$p]['libras'] = $libra == 0 ? '-' : $this->numeroFormato($libra/$presupuesto->libra);
            }


            foreach ($presupuesto->partidas as $partida) {
                if (key_exists($partida->id_concepto, $precios)) {
                    if($partida->precio_unitario_compuesto > 0 && $precios[$partida->id_concepto] > $partida->precio_unitario_compuesto)
                        $precios[$partida->id_concepto] = (float) $partida->precio_unitario_compuesto;
                } else {
                    if($partida->precio_unitario_compuesto > 0) {
                        $precios[$partida->id_concepto] = (float) $partida->precio_unitario_compuesto;
                    }
                }

                if (array_key_exists($partida->id_concepto, $partidas)) {
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['id_transaccion'] = $presupuesto->id_transaccion;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario'] = $partida->precio_unitario;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario_simple'] = $partida->precio_unitario_simple;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['id_moneda'] = $partida->IdMoneda;
                   $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_total_compuesto'] = $partida->precio_compuesto_total;
                   $partidas[$partida->id_concepto]['presupuestos'][$p]['precio_unitario_compuesto'] = $partida->precio_unitario_compuesto;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['tipo_cambio_descripcion'] = $partida->moneda ? $partida->moneda->abreviatura : '';
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['descuento_partida'] = $partida->PorcentajeDescuento;
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['observaciones'] = $partida->Observaciones;
                    $partida_asignada = $this->partidas->where('id_concepto', $partida->id_concepto)->where('id_transaccion', $presupuesto->id_transaccion)->first();
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['cantidad_asignada'] = $partida_asignada ? number_format($partida_asignada->cantidad_autorizada,2, '.', ',') : '-';
                    $partidas[$partida->id_concepto]['presupuestos'][$p]['importe_asignado'] = $partida_asignada ? number_format($partida_asignada->importe_calculado, 2, '.',',') : '-';
                }
            }
        }

       /* foreach ($this->solicitud->cotizaciones as $cont => $cotizacion) {
            $cotizaciones[$cont]['ivg_partida'] = $this->calcular_ivg($precios, $cotizacion->partidas);
            $cotizaciones[$cont]['ivg_partida_porcentaje'] = $cotizacion->partidas->count() > 0 ? $cotizaciones[$cont]['ivg_partida']/ $cotizacion->partidas->count() : 0 ;
        }*/
        return [
            'presupuestos' => $presupuestos,
            'partidas' => $partidas,
            //'precios_menores' => $precios
        ];
    }

    public function sumaSubtotalPartidas($tipo_moneda)
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            if($tipo_moneda == $partida->partidaPresupuesto->IdMoneda)
            {
                $suma += $partida->importe_calculado;
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
}
