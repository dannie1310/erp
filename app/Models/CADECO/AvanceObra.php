<?php


namespace App\Models\CADECO;


use App\Models\CADECO\AvanceObra\AvanceObraEliminado;
use App\Models\CADECO\AvanceObra\AvanceObraPartidaEliminada;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;

class AvanceObra extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 98;
    public const OPCION = 0;
    public const NOMBRE = "Avance de Obra";

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'opciones',
        'fecha',
        'id_concepto',
        'observaciones',
        'comentario',
        'FechaHoraRegistro',
        'id_obra',
        'id_usuario',
        'cumplimiento',
        'vencimiento'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query)
        {
            return $query->where('tipo_transaccion', self::TIPO)->where('opciones', self::OPCION);
        });
    }

    /**
     * Relaciones
     */
    public function partidas()
    {
        return $this->hasMany(ItemAvanceObra::class, 'id_transaccion', 'id_transaccion');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    /**
     * Scope
     */
    public function scopeRegistrado($query)
    {
        return $query->where('estado', 0);
    }

    /**
     * Attributos
     */
    public function getConceptoDescripcionAttribute()
    {
        try{
            return $this->concepto->descripcion;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getConceptoNivelAttribute()
    {
        try{
            return $this->concepto->nivel;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ((int) $this->estado) {
            case 0:
                return '#f39c12';
                break;
            case 1:
                return '#4f9b34';
                break;
            case 2:
                return '#2C6AA4';
                break;
        }
    }

    public function getDescripcionEstadoAttribute()
    {
        switch ((int) $this->estado) {
            case 0:
                return 'Registrada';
                break;
            case 1:
                return 'Aprobada';
                break;
            case 2:
                return 'Cerrada';
                break;
        }
    }

    public function getTotalAttribute()
    {
        return $this->subtotal + $this->impuesto;
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format($this->total, 2, '.', ',');
    }

    /**
     * Métodos
     */
    public function registrar(array $data)
    {
        $this->validarConceptos($data['conceptos']['data']);
        try
        {
            DB::connection('cadeco')->beginTransaction();
            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $cumplimiento = New DateTime($data['fecha_inicio']);
            $cumplimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $vencimiento = New DateTime($data['fecha_termino']);
            $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

            $avance = $this->create([
                'fecha' => $fecha->format("Y-m-d"),
                'cumplimiento' => $cumplimiento->format("Y-m-d"),
                'vencimiento' => $vencimiento->format("Y-m-d"),
                'id_concepto' => $data['id_concepto_padre'],
                'observaciones' => $data['observaciones']
            ]);

            $avance->registrarPartidas($data['conceptos']['data']);
            $avance->actualizarTotales();
            DB::connection('cadeco')->commit();
            return $avance;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    private function registrarPartidas($conceptos)
    {
        foreach ($conceptos as $concepto)
        {
            if(($concepto['concepto_medible'] == 1 || $concepto['concepto_medible'] == 3) && (float) $concepto['avance'] != 0)
            {
                $conc = Concepto::where('id_concepto', $concepto['id_concepto'])->first();
                $precio_prod = $conc->precioVenta ? $conc->precioVenta->precio_produccion : 0;
                $this->partidas()->create([
                    'id_transaccion' => $this->id_transaccion,
                    'id_concepto' => $concepto['id_concepto'],
                    'cantidad' => $concepto ['avance'],
                    'precio_unitario' => $precio_prod,
                    'importe' => $concepto ['avance'] * $precio_prod,
                    'numero' => $concepto['cumplido']
                ]);
            }
        }
    }

    private function actualizarTotales()
    {
        $subtotal = ItemAvanceObra::where('id_transaccion', $this->id_transaccion)->selectRaw('SUM([importe]) as subtotal')->first()->subtotal;
        $iva = ($subtotal * $this->obra->iva) / 100;
        $this->monto = $subtotal + $iva;
        $this->impuesto = $iva;
        $this->save();
    }

    public function aprobar()
    {
        $this->validar('aprobar');
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->cambioAnteriorEstatus();
            $this->estado = 1;
            $this->save();
            $this->editarCantidadConceptos();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    public function actualizar($data){
        try{
            DB::connection('cadeco')->beginTransaction();
            if($this->estado != 0){
                throw new Exception("Este avance de obra se encuentra aprobado no se puede editar.");
            }
            $cumplimiento = New DateTime($data['fecha_inicio']);
            $cumplimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $vencimiento = New DateTime($data['fecha_termino']);
            $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $this->cumplimiento = $cumplimiento->format("Y-m-d");
            $this->vencimiento = $vencimiento->format("Y-m-d");
            $this->save();
            $this->actualizarPartidas($data['conceptos']);
            DB::connection('cadeco')->commit();
            return $this;
        }catch(\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validar($tipo_validacion)
    {
        if($tipo_validacion == 'aprobar')
        {
            if ($this->estado != 0) {
                abort(400, "El avance de obra ya fue aprobada previamente.");
            }
        }
        if($tipo_validacion == 'revertir')
        {
            if ($this->estado == 0) {
                abort(400, "El avance de obra no esta aprobada previamente.");
            }
        }
        if($tipo_validacion == 'eliminar')
        {
            if($this->estado != 0)
            {
                abort(400, "Este avance de obra se encuentra aprobado no se puede eliminar.");
            }
        }
    }

    private function cambioAnteriorEstatus()
    {
        $avances = self::where('estado', 1)->where('id_transaccion', '<', $this->id_transaccion)->orderBy('id_transaccion','desc')->get();
        foreach ($avances as $avance) {
            $avance->estado = 2;
            $avance->save();
        }
    }

    private function editarCantidadConceptos()
    {
        foreach ($this->partidas as $partida)
        {
            $partida->concepto->cantidad_ejecutada = $partida->concepto->cantidad_ejecutada + $partida->cantidad;
            $partida->concepto->estado = $partida->numero == 1 ? 16 : 0;
            $partida->concepto->save();
        }
    }

    public function revertir()
    {
        $this->validar('revertir');
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->revertirCambioAnteriorEstatus();
            $this->estado = 0;
            $this->save();
            $this->revertirEdicionCantidadConceptos();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    private function revertirCambioAnteriorEstatus()
    {
        $avance = self::where('estado', 2)->where('id_transaccion', '<', $this->id_transaccion)->orderBy('id_transaccion', 'desc')->first();
        if($avance) {
            $avance->estado = 1;
            $avance->save();
        }
    }

    private function revertirEdicionCantidadConceptos()
    {
        foreach ($this->partidas as $partida)
        {
            $partida->concepto->cantidad_ejecutada = $partida->concepto->cantidad_ejecutada - $partida->cantidad;
            $partida->concepto->estado = 0;
            $partida->concepto->save();
        }
    }

    public function eliminar($motivo)
    {
        $this->validar('eliminar');
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->delete();
            $this->revisarRespaldos($motivo);
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Elimina las partidas
     */
    public function eliminarPartidas()
    {
        foreach ($this->partidas()->get() as $item) {
            $item->delete();
        }
    }

    private function revisarRespaldos($motivo)
    {
        if (($avance = AvanceObraEliminado::where('id_transaccion', $this->id_transaccion)->first()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del avance de obra, no se respaldo el avance de obra correctamente.');
        } else {
            $avance->motivo = $motivo;
            $avance->save();
        }
        if (($item = AvanceObraPartidaEliminada::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del avance de obra, no se respaldo las partidas correctamente.');
        }
    }

    public function actualizarPartidas($partidas){
        foreach($partidas as $partida){
            $item = ItemAvanceObra::find($partida['id']);
            if(($partida['cantidad'] + $item->concepto->cantidad_presupuestada) < 0){
                throw new Exception("El avance de obra del concepto '" . $item->concepto->descripcion . "' debe ser igual o mayor a " . -$item->concepto->cantidad_presupuestada);
            }
            $item->cantidad = $partida['cantidad_format'];
            $item->numero = $partida['cumplido']?1:0;
            $item->save();
        }
    }

    private function validarConceptos($conceptos)
    {
        $mensaje = "";
        foreach ($conceptos as $concepto)
        {
            if ($concepto['concepto_medible'] == 3 && (float)$concepto['avance'] != 0) {
                if ($concepto['estado'] != 0) {
                    $mensaje = $mensaje . "-El concepto ".$concepto['descripcion']." se encuentra con estado cumplido.\n";
                }
                $avance_item = ItemAvanceObra::where('id_concepto', $concepto['id_concepto'])->get();
                foreach ($avance_item as $item) {
                    if ($item->avance_obra_activo) {
                        $mensaje = $mensaje . "-El concepto " . $item->concepto_descripcion . " existe en el avance de obra registrada con número: " . $item->avanceObra->numero_folio_format . ". \n";
                    }
                }
            }
        }
        if ($mensaje != "")
        {
            abort(400, "No se puede registrar el avance de obra debido a los siguientes problemas:\n" . $mensaje);
        }
    }
}
