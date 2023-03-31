<?php


namespace App\Models\CADECO\Compras;


use App\Models\IGH\Usuario;
use App\Models\CADECO\Cambio;
use App\Models\IGH\TipoCambio;
use App\Models\CADECO\OrdenCompra;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\CotizacionCompra;
use Illuminate\Database\Eloquent\Model;
use Dingo\Blueprint\Annotation\Attributes;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;

class AsignacionProveedor extends Model
{
    protected $connection = 'cadeco';
    protected $table      = 'Compras.asignacion_proveedores';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_transaccion_solicitud',
        'observaciones',
        'estado',
        'registro',
        'origen'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('solicitud');
        });
    }

    /**
     * Relaciones
     */
    public function estadoAsignacion()
    {
        return $this->belongsTo(CtgEstadoAsignacionProveedor::class, 'estado', 'id');
    }

    public function partidas()
    {
        return $this->hasMany(AsignacionProveedorPartida::class, 'id_asignacion_proveedores', 'id');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCompra::class, 'id_transaccion_solicitud', 'id_transaccion');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function ordenCompraComplemento()
    {
        return $this->belongsTo(OrdenCompraComplemento::class, 'id', 'id_asignacion_proveedor');
    }

    public function ordenCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'id_antecedente', 'id_transaccion_solicitud');
    }

    /**
     * Scopes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', '=', 1);
    }

    public function scopeAreasCompradorasAsignadas($query)
    {
        return $query->whereHas('solicitud', function ($q) {
            $q->areasCompradorasAsignadas();
        });
    }

    /**
     * Attributes
     */
    public function getFechaFormatAttribute()
    {
        $date = date_create($this->timestamp_registro);
        return date_format($date, "d/m/Y H:i");
    }

    public function getFolioFormatAttribute()
    {
        return '#' . sprintf("%05d", $this->id);
    }

    public function getEstadoAsignacionFormatAttribute()
    {
        return $this->estadoAsignacion->descripcion;
    }

    public function getOrdenCompraPendienteAttribute()
    {
        $partidas = $this->partidas;
        $cant = $partidas->count();
        foreach ($partidas as $partida) {
            $ordenes_c = $partida->ordenCompra;
            foreach ($ordenes_c as $orden) {
                if ($orden->complemento->id_asignacion_proveedor == $this->id) {
                    $cant--;
                    break;
                }
            }
        }
        return $cant;
    }

    public function getSumaSubtotalPartidasAttribute()
    {
        $suma = 0;
        foreach ($this->partidas as $partida) {
            $suma += $partida->total_precio_moneda;
        }
        return $suma;
    }

    public function getMejorAsignadoAttribute()
    {
        $suma_mejor_asignado = 0;
        $valor_calculado = 0;
        $suma_mejor_por_partida = 0;
        $dolar = $this->tipo_cambio(2);
        $euro = $this->tipo_cambio(3);
        $libra = $this->tipo_cambio(4);
        $materiales = $this->partidas()->groupBy('id_material')->pluck('id_material');
        foreach ($materiales as $material) {
            $partida_asignacion = $this->partidas()->where('id_material', $material)->first();
            foreach ($this->solicitud->cotizaciones as $cotizacion) {
                $partida_encontrada = $cotizacion->partidas()->where('id_material', '=', $material)->first();
                if ($partida_encontrada) {
                    switch ($partida_encontrada->id_moneda) {
                        case (1):
                            $valor_calculado = $partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto;
                            break;
                        case (2):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $dolar);
                            break;
                        case (3):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $euro);
                            break;
                        case (4):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $libra);
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
        $suma_mejor_asignado = 0;
        $valor_calculado = 0;
        $suma_mejor_por_partida = 0;
        $dolar = $this->tipo_cambio(2);
        $euro = $this->tipo_cambio(3);
        $libra = $this->tipo_cambio(4);
        $materiales = $this->partidas()->groupBy('id_material')->pluck('id_material');
        foreach ($materiales as $material) {
            $partida_asignacion = $this->partidas()->where('id_material', $material)->first();
            foreach ($this->solicitud->cotizaciones as $cotizacion) {
                $partida_encontrada = $cotizacion->partidas()->where('id_material', '=', $material)->first();
                if ($partida_encontrada) {
                    switch ($partida_encontrada->id_moneda) {
                        case (1):
                            $valor_calculado = $partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto;
                            break;
                        case (2):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $dolar);
                            break;
                        case (3):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $euro);
                            break;
                        case (4):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $libra);
                            break;
                    }

                    if ($suma_mejor_por_partida === 0) {
                        $suma_mejor_por_partida = $valor_calculado * $cotizacion->tasa_iva;
                    }
                    if ($valor_calculado < $suma_mejor_por_partida) {
                        $suma_mejor_por_partida = $valor_calculado * $cotizacion->tasa_iva;
                    }
                }
            }
            $suma_mejor_asignado = $suma_mejor_asignado + (float)$suma_mejor_por_partida;
            $suma_mejor_por_partida = 0;
        }
        return $suma_mejor_asignado;
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

    public function getSumaTotalConDescuentoAttribute()
    {
        $suma_global = 0;
        $suma = 0;
        foreach ($this->solicitud->cotizaciones as $cotizacion) {
            foreach ($cotizacion->asignacionPartida->where('id_asignacion_proveedores', $this->id) as $asignacion) {
                $suma += $asignacion->total_precio_moneda;
            }

            if ($suma != 0 && $cotizacion->complemento) {
                $suma -= $suma * $cotizacion->complemento->descuento / 100;
            }
            $suma_global += $suma;
            $suma = 0;
        }
        return $suma_global;
    }

    public function getSumaSubtotalPartidasIvaAttribute()
    {
        $suma_global = 0;
        $suma = 0;
        foreach ($this->solicitud->cotizaciones as $cotizacion) {
            foreach ($cotizacion->asignacionPartida->where('id_asignacion_proveedores', $this->id) as $asignacion) {
                $suma += $asignacion->total_precio_moneda;
            }

            if ($suma != 0 && $cotizacion->complemento) {
                $suma -= $suma * $cotizacion->complemento->descuento / 100;
            }
            $suma_global += $suma * $cotizacion->tasa_iva;
            $suma = 0;
        }
        return $suma_global;
    }

    public function getSumaSubtotalPartidasTotalAttribute()
    {
        return $this->suma_total_con_descuento + $this->suma_subtotal_partidas_iva;
    }

    public function getAplicadaAttribute()
    {
        if ($this->ordenCompraComplemento) {
            if ($this->ordenCompraComplemento->count() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getMejoresOpcionesEncapsuladoPorMaterialAttribute()
    {
        $array = [];
        $valor_calculado = 0;
        $suma_mejor_por_partida = 0;
        $id_cotizacion_optima = null;
        foreach ($this->solicitud->partidas()->groupBy('id_material')->pluck('id_material') as $material) {
            $partida_asignacion = $this->partidas()->where('id_material', $material)->first();
            foreach ($this->solicitud->cotizaciones as $cotizacion) {
                $partida_encontrada = $cotizacion->partidas()->where('id_material', '=', $material)->first();
                if ($partida_encontrada && $partida_asignacion) {
                    switch ($partida_encontrada->id_moneda) {
                        case (1):
                            $valor_calculado = $partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto;
                            break;
                        case (2):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $this->tipo_cambio(2));
                            break;
                        case (3):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $this->tipo_cambio(3));
                            break;
                        case (4):
                            $valor_calculado = ($partida_asignacion->suma_cantidad_asignada * $partida_encontrada->precio_compuesto * $this->tipo_cambio(4));
                            break;
                    }
                    if ($suma_mejor_por_partida === 0) {
                        $id_cotizacion_optima = $partida_encontrada->id_transaccion;
                        $suma_mejor_por_partida = $valor_calculado;
                    }
                    if ($valor_calculado < $suma_mejor_por_partida) {
                        $id_cotizacion_optima = $partida_encontrada->id_transaccion;
                        $suma_mejor_por_partida = $valor_calculado;
                    }
                }
            }
            if (!array_key_exists($material, $array)) {
                $array[$material] = $id_cotizacion_optima;
            }
            $valor_calculado = 0;
            $suma_mejor_por_partida = 0;
            $id_cotizacion_optima = null;
        }
        return $array;
    }

    public function getUsuarioAttribute()
    {
        if ($this->usuarioRegistro) {
            return $this->usuarioRegistro->nombre_completo;
        } else {
            return $this->comentario;
        }
    }

    public function getAsignacionParcialAttribute(){
        if($this->solicitud->partidas->count() != $this->partidas->count()){
            return true;
        }

        foreach($this->partidas as $partida){
            if($partida->cantidad_asignada != $partida->itemSolicitud->cantidad){
                return true;
            }
        }
        return false;
    }

    /**
     * Métodos
     */
    public function datosPartidas()
    {
        $items = array();
        foreach ($this->partidas as $partida) {
            $items[] = array(
                'item_solicitud' => $partida->id_item_solicitud,
                'id_material' => $partida->id_material,
                'id_transaccion_cotizacion' => $partida->id_transaccion_cotizacion,
                'cantidad_asignada' => $partida->cantidad_asignada,
                'id_empresa' => $partida->cotizacionCompra->id_empresa,
                'usuario_registro' => $partida->registro
            );
        }
        return $items;
    }

    public function validaAsociadaOrdenCompra()
    {
        if ($this->ordenCompraComplemento) {
            throw New \Exception("La Asignacion " . $this->folio_format . " ya se encuentra asociada a una Orden de Compra");
        }
    }

    public function eliminarAsignacion($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->delete();
            $eliminada = AsignacionProveedorEliminada::find($this->id);
            $eliminada->motivo_elimino = $motivo;
            $eliminada->save();
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function subtotalPorCotizacion($id_cotizacion)
    {
        $suma = 0;
        foreach ($this->partidas as $partida) {
            if ($partida->id_transaccion_cotizacion == $id_cotizacion) {
                $suma += $partida->total_precio_moneda;
            }
        }
        return $suma;
    }

    public function tipo_cambio($tipo)
    {
        $tipo_cambio = Cambio::where('id_moneda', '=', $tipo)->where('fecha', '=', $this->timestamp_registro)->first();
        return $tipo_cambio ? $tipo_cambio->cambio : $tipo_cambio = Cambio::where('id_moneda', '=', $tipo)->orderByDesc('fecha')->first()->cambio;
    }
}
