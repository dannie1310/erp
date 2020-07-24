<?php


namespace App\Models\CADECO\Compras;


use App\Models\IGH\Usuario;
use App\Models\CADECO\SolicitudCompra;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\CotizacionCompra;
use Illuminate\Support\Facades\DB;

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
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('solicitud');
        });
    }

    public function estadoAsignacion(){
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

    public function scopePendientes($query){
        return $query->where('estado', '=', 1);
    }

    public function getFechaFormatAttribute(){
        $date = date_create($this->timestamp_registro);
        return date_format($date,"d/m/Y");
    }

    public function getFolioFormatAttribute(){
        return '#' . sprintf("%05d", $this->id);
    }

    public function datosPartidas()
    {
        $items = array();
        foreach($this->partidas as $partida)
        {
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
        if($this->ordenCompraComplemento)
        {
            throw New \Exception("La Asignacion ". $this->folio_format. " ya se encuentra asociada a una Orden de Compra");
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

    public function getEstadoAsignacionFormatAttribute(){
        $total = count($this->partidas);
        $con_Orden = 0;
        foreach($this->partidas as $partida){
            if($partida->con_orden_compra){
                $con_Orden++;
            }
        }
        $res = $total > $con_Orden?1:2;
        $this->estado = $res;
        $this->save();
        return $this->estadoAsignacion->descripcion;
    }

    public function getOrdenCompraPendienteAttribute(){
        $partidas = $this->partidas;
        foreach($partidas as $partida){
            if(!$partida->con_orden_compra){
                return true;
            }
        }
        return false;
    }

    public function getSumaSubtotalPartidasAttribute()
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            $suma += $partida->total_precio_moneda;
        }
        return $suma;
    }

    public function getMejorAsignadoAttribute()
    {
        $suma_mejor_asignado = 0;
        $valor_calculado = 0;
        $suma_mejor_por_partida = 0;
        foreach ($this->partidas as $partida_asignacion) {
            foreach ($partida_asignacion->asignacion->solicitud->cotizaciones as $cotizacion) {
               $partida_encontrada = $cotizacion->partidas()->where('id_material','=',$partida_asignacion->id_material)->first();
               if($partida_encontrada) {
                   switch ($partida_encontrada->id_moneda) {
                       case (1):
                           $valor_calculado = $partida_asignacion->cantidad_asignada * $partida_encontrada->precio_compuesto;
                           break;
                       case (2):
                           $valor_calculado =  ($partida_asignacion->cantidad_asignada * $cotizacion->precio_compuesto * $cotizacion->complemento->tc_usd);
                           break;
                       case (3):
                           $valor_calculado =  ($partida_asignacion->cantidad_asignada * $cotizacion->precio_compuesto * $cotizacion->complemento->tc_eur);
                           break;
                   }
                   if($suma_mejor_por_partida == 0)
                   {
                       $suma_mejor_por_partida = $valor_calculado;
                   }
                   if($valor_calculado < $suma_mejor_por_partida)
                   {
                        $suma_mejor_por_partida = $valor_calculado;
                   }
               }
            }
            $suma_mejor_asignado += $suma_mejor_por_partida;
        }
        return $suma_mejor_asignado;
    }
}
