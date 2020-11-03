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

    public function scopeProyectado($query)
    {
        return $query->has('contratoProyectado');
    }

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
            dd($presupuesto);
            $presupuestos[$p]['id_transaccion'] = $presupuesto->id_transaccion;
            $presupuestos[$p]['empresa'] = $presupuesto->empresa->razon_social;
            $presupuestos[$p]['fecha'] = $presupuesto->fecha_format;
            $presupuestos[$p]['vigencia'] = $presupuesto->complemento ? $cotizacion->complemento->vigencia : '-';
            $presupuestos[$p]['anticipo'] = $presupuesto->complemento ? $cotizacion->complemento->anticipo : '-';
            $presupuestos[$p]['dias_credito'] = $presupuesto->complemento ? $cotizacion->complemento->dias_credito : '-';
            $presupuestos[$p]['descuento_global'] = ($cotizacion->complemento && $cotizacion->complemento->descuento > 0) ? $cotizacion->complemento->descuento : '-';
         /*   $presupuestos[$p]['suma_subtotal_partidas'] = $cotizacion->suma_subtotal_partidas;
            $presupuestos[$p]['iva_partidas'] = $cotizacion->iva_partidas;
            $presupuestos[$p]['total_partidas'] = $cotizacion->total_partidas;
            $presupuestos[$p]['tipo_moneda'] = $cotizacion->moneda ? $cotizacion->moneda->nombre : '';
            $presupuestos[$p]['observaciones'] = $cotizacion->complemento ? $cotizacion->complemento->observaciones : '-';
            $presupuestos[$cont]['tc_usd'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_usd ? $cotizacion->complemento->tc_usd :Cambio::where('id_moneda','=', 2)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $presupuestos[$cont]['tc_eur'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_eur ? $cotizacion->complemento->tc_eur : Cambio::where('id_moneda','=', 3)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $presupuestos[$cont]['tc_libra'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_libra ? $cotizacion->complemento->tc_libra : Cambio::where('id_moneda','=', 4)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $presupuestos[$cont]['subtotal_peso'] = $cotizacion->sumaSubtotalPartidas(1) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(1), 2, '.', ',');
            $presupuestos[$cont]['subtotal_dolar'] = $cotizacion->sumaSubtotalPartidas(2) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(2), 2, '.', ',');
            $presupuestos[$cont]['subtotal_euro'] = $cotizacion->sumaSubtotalPartidas(3) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(3), 2, '.', ',');
            $presupuestos[$cont]['subtotal_libra'] = $cotizacion->sumaSubtotalPartidas(4)== 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(4), 2, '.', ',');
            $presupuestos[$cont]['suma_total_dolar'] = $cotizacion->sumaPrecioPartidaMoneda(2) == 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(2), 2, '.', ',');
            $presupuestos[$cont]['suma_total_euro'] = $cotizacion->sumaPrecioPartidaMoneda(3) == 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(3), 2, '.', ',');
            $presupuestos[$cont]['suma_total_libra'] = $cotizacion->sumaPrecioPartidaMoneda(4)== 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(4), 2, '.', ',');
           */
            foreach ($cotizacion->partidas as $p) {
                if (key_exists($p->id_material, $precios)) {
                    if($p->precio_unitario_compuesto > 0 && $precios[$p->id_material] > $p->precio_unitario_compuesto)
                        $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                } else {
                    if($p->precio_unitario_compuesto > 0) {
                        $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                    }
                }
                if (array_key_exists($p->id_material, $partidas)) {
                    $partidas[$p->id_material]['cotizaciones'][$cont]['id_transaccion'] = $cotizacion->id_transaccion;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['cantidad'] = $p->cantidad;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_unitario'] = $p->precio_unitario;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['id_moneda'] = $p->id_moneda;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['cantidad_format'] = $p->cantidad_format;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_total_moneda'] = $p->total_precio_moneda;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_con_descuento'] = $p->precio_compuesto;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_total_compuesto'] = $p->precio_compuesto_total;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_unitario_compuesto'] = $p->precio_unitario_compuesto;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['tipo_cambio_descripcion'] = $p->moneda ? $p->moneda->abreviatura : '';
                    $partidas[$p->id_material]['cotizaciones'][$cont]['descuento_partida'] = $p->partida ? $p->partida->descuento_partida : 0;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['observaciones'] = $p->partida ? $p->partida->observaciones : '';
                }
            }
        }
        foreach ($this->solicitud->cotizaciones as $cont => $cotizacion) {
            $cotizaciones[$cont]['ivg_partida'] = $this->calcular_ivg($precios, $cotizacion->partidas);
            $cotizaciones[$cont]['ivg_partida_porcentaje'] = $cotizacion->partidas->count() > 0 ? $cotizaciones[$cont]['ivg_partida']/ $cotizacion->partidas->count() : 0 ;
        }
        return [
            'cotizaciones' => $cotizaciones,
            'partidas' => $partidas,
            'precios_menores' => $precios
        ];
    }
}
