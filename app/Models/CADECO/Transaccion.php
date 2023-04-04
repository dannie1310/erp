<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 06:00 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Documentacion\Archivo;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Carbon\Carbon;
use App\Facades\Context;
use App\Models\IGH\Usuario;
use App\Models\CADECO\Fondo;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\SEGURIDAD_ERP\CtgContratista;
use App\Models\CADECO\Contabilidad\HistPoliza;
use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Transaccion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'transacciones';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'estado'
    ];

    public $timestamps = false;

    protected $dates = ['cumplimiento'];

    //protected $dateFormat = 'Y-m-d H:i:s';

    public const CREATED_AT = 'FechaHoraRegistro';
    public const TIPO_ANTECEDENTE = 0;
    public const OPCION_ANTECEDENTE = 0;
    public const SHOW_ROUTE = "";
    public const ARTICULO = "La";

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            /*if(auth()->user()->id_contratista){
                if(($contratista = CtgContratista::query()->find(auth()->user()->id_contratista)) && auth()->user()->usuario_estado == 3){
                    $query->where('id_empresa', '=', $contratista->empresa->id_empresa);
                }else{
                    abort(403, 'Contratista no registrado.');
                }
            }*/
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, "id_transaccion", "id_transaccion");
    }

    public function getUsuarioRegistroAttribute()
    {
        if($this->usuario)
        {
            return $this->usuario->nombre_completo;
        } else{
            return $this->comentario;
        }
    }

    public function facturasRepositorio()
    {
        return $this->hasMany(FacturaRepositorio::class, 'id_transaccion', 'id_transaccion')
            ->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())
                ->first()->getKey());
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getMontoFormatAttribute()
    {
        return '$' . number_format(($this->monto),2);
    }

    public function getSaldoFormatAttribute()
    {
        return '$' . number_format($this->saldo,2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaVencimientoFormatAttribute()
    {
        $date = date_create($this->vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function tipo()
    {
        return $this->belongsTo(TipoTransaccion::class, 'tipo_transaccion', 'tipo_transaccion');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'id_transaccion', 'id_transaccion');
    }

    public function invitacion()
    {
        return $this->belongsTo(Invitacion::class, "id_referente", "id");
    }

    public function cotizaciones()
    {
        if($this->invitacion && $this->tipo_transaccion == 18)
        {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $this->invitacion->base_datos);
        }
        return $this->hasMany(CotizacionCompraPartida::class, 'id_transaccion', 'id_transaccion')->withoutGlobalScopes();
    }

    public function presupuestos()
    {
        if($this->invitacion && $this->tipo_transaccion == 50)
        {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $this->invitacion->base_datos);
        }
        return $this->hasMany(PresupuestoContratistaPartida::class, 'id_transaccion', 'id_transaccion')->withoutGlobalScopes();
    }

    public function validaTipoAntecedente()
    {
        if(!is_null($this::TIPO_ANTECEDENTE))
        {
            if(!is_null(Context::getIdObra()))
            {
                $antecedente = Transaccion::query()->withoutGlobalScope('tipo')->find($this->id_antecedente);
            }else{
                $antecedente = Transaccion::withoutGlobalScopes()->where('id_transaccion', $this->id_antecedente)->where('id_obra', $this->id_obra)->first();
            }
            if($antecedente->tipo_transaccion != $this::TIPO_ANTECEDENTE || $antecedente->opciones != $this::OPCION_ANTECEDENTE)
            {
                return false;
            }
        }
        return true;
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function obra()
    {
        if($this->invitacion && ($this->tipo_transaccion == 18 || $this->tipo_transaccion == 50))
        {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $this->invitacion->base_datos);
        }
        return $this->belongsTo(Obra::class, 'id_obra', 'id_obra');
    }

    public function getDatosRegistroAttribute()
    {
        $salida = "Registrada el ".$this->fecha_hora_registro_format;
        if($this->usuario){
            $salida.=" por ". $this->usuario->nombre_completo;
        }
        return $salida;
    }

    public function getCumplimientoAttribute($cumplimiento)
    {
        return substr($cumplimiento, 0, 10);
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"d/m/Y H:i");
    }

    public function getHoraRegistroAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"H:i");
    }

    public function getFechaRegistroAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"d/m/Y");
    }

    public function getFechaHoraRegistroOrdenAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"YmdHis");
    }

    public function getCumplimientoFormAttribute()
    {
        $date = date_create($this->cumplimiento);
        return date_format($date,"d/m/Y");
    }

    public function getVencimientoFormAttribute()
    {
        $date = date_create($this->vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function getVencimientoFormatAttribute()
    {
        $date = date_create($this->vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function getCumplimientoFormatAttribute()
    {
        $date = date_create($this->cumplimiento);
        return date_format($date,"d/m/Y");
    }

    public function  getObservacionesFormatAttribute(){
        if(strlen($this->observaciones)>60){
            return mb_substr($this->observaciones,0,60, 'UTF-8')."...";
        } else {
            return $this->observaciones;
        }
    }

    public function getTipoTransaccionStrAttribute()
    {
        switch ($this->tipo_transaccion){
            case  17: return SolicitudCompra::NOMBRE;
            case  18: return CotizacionCompra::NOMBRE;
            case  19: return OrdenCompra::NOMBRE;
            case  33: return EntradaMaterial::NOMBRE;
            case  34: return SalidaAlmacen::NOMBRE;
            case  49: return ContratoProyectado::NOMBRE;
            case  50: return PresupuestoContratista::NOMBRE;
            case  51: return Subcontrato::NOMBRE;
            case  52: return Estimacion::NOMBRE;
            case  65: return Factura::NOMBRE;
            case  72: return SolicitudPagoAnticipado::NOMBRE;
            case  81: return Deposito::NOMBRE;
            case  82: return Pago::NOMBRE;
            case  83: return Credito::NOMBRE;
            case  84: return Debito::NOMBRE;
            case  101: return ComprobanteFondo::NOMBRE;
            default: try{return $this->tipo->Descripcion;} catch (\Exception $e){ return "";}
        }
    }

    public function getArticuloTipoTransaccionStrAttribute()
    {
        switch ($this->tipo_transaccion){
            case  17: return SolicitudCompra::ARTICULO;
            case  18: return CotizacionCompra::ARTICULO;
            case  19: return OrdenCompra::ARTICULO;
            case  33: return EntradaMaterial::ARTICULO;
            case  34: return SalidaAlmacen::ARTICULO;
            case  49: return ContratoProyectado::ARTICULO;
            case  50: return PresupuestoContratista::ARTICULO;
            case  51: return Subcontrato::ARTICULO;
            case  52: return Estimacion::ARTICULO;
            case  65: return Factura::ARTICULO;
            case  82: return Pago::ARTICULO;
            case  72: return SolicitudPagoAnticipado::ARTICULO;
            default: try{return $this->tipo->Descripcion;} catch (\Exception $e){ return "";}
        }
    }

    public function getIconoAttribute()
    {
        switch ($this->tipo_transaccion){
            case  17: return SolicitudCompra::ICONO;
            case  18: return CotizacionCompra::ICONO;
            case  19: return OrdenCompra::ICONO;
            case  33: return EntradaMaterial::ICONO;
            case  34: return SalidaAlmacen::ICONO;
            case  49: return ContratoProyectado::ICONO;
            case  50: return PresupuestoContratista::ICONO;
            case  51: return Subcontrato::ICONO;
            case  52: return Estimacion::ICONO;
            case  65: return Factura::ICONO;
            case  82: return Pago::ICONO;
            case  72: return SolicitudPagoAnticipado::ICONO;
            default:  return "";
        }
    }

    public function getTransaccionAttribute()
    {

        switch ($this->tipo_transaccion){
            case  17: return SolicitudCompra::find($this->id_transaccion);
            case  18: return CotizacionCompra::find($this->id_transaccion);
            case  19: return OrdenCompra::find($this->id_transaccion);
            case  33: return EntradaMaterial::find($this->id_transaccion);
            case  34: return SalidaAlmacen::find($this->id_transaccion);
            case  49: return ContratoProyectado::find($this->id_transaccion);
            case  50: return PresupuestoContratista::find($this->id_transaccion);
            case  51: return Subcontrato::find($this->id_transaccion);
            case  52: return Estimacion::find($this->id_transaccion);
            case  65: return Factura::find($this->id_transaccion);
            case  82: return Pago::find($this->id_transaccion);
            case  72: return SolicitudPagoAnticipado::find($this->id_transaccion);
            default:  return "";
        }

    }

    public function getRelacionesAttribute()
    {
        switch ($this->tipo_transaccion){
            case  17: return SolicitudCompra::find($this->id_transaccion)->relaciones;
            case  18: return CotizacionCompra::find($this->id_transaccion)->relaciones;
            case  19: return OrdenCompra::find($this->id_transaccion)->relaciones;
            case  33: return EntradaMaterial::find($this->id_transaccion)->relaciones;
            case  34: return SalidaAlmacen::find($this->id_transaccion)->relaciones;
            case  49: return ContratoProyectado::find($this->id_transaccion)->relaciones;
            case  50: return PresupuestoContratista::find($this->id_transaccion)->relaciones;
            case  51: return Subcontrato::find($this->id_transaccion)->relaciones;
            case  52: return Estimacion::find($this->id_transaccion)->relaciones;
            case  65: return Factura::find($this->id_transaccion)->relaciones;
            case  82: return Pago::find($this->id_transaccion)->relaciones;
            case  72: return SolicitudPagoAnticipado::find($this->id_transaccion)->relaciones;
            case  72:
                if($sol_p_a = SolicitudPagoAnticipado::find($this->id_transaccion)){
                    return $sol_p_a->relaciones;
                }
                return [];
            default:  return [];
        }
    }

    public function getArrMonedasAttribute()
    {
        $monedas = [];
        foreach ($this->cotizaciones()->activa()->get() as $partida)
        {
            $monedas[$partida->id_moneda] = $partida->id_moneda;
        }
        return $monedas;
    }

    public function getArrMonedasPresupuestoAttribute()
    {
        $monedas = [];
        foreach ($this->presupuestos()->cotizadas()->get() as $partida)
        {
            $monedas[$partida->IdMoneda] = $partida->IdMoneda;
        }
        return $monedas;
    }

    public function getMultimonedaAttribute()
    {
        $multimoneda = false;
        $monedas = [];
        if($this->tipo_transaccion == 18)
        {
            $monedas = $this->arr_monedas;
        }
        if($this->tipo_transaccion == 50)
        {
            $monedas = $this->arr_monedas_presupuesto;
        }

        if(count($monedas)>1){
            $multimoneda = true;
        }
        return $multimoneda;
    }

    public function getMontoConsultaProveedorFormatAttribute()
    {
        if($this->multimoneda)
        {
            return $this->monto_format;
        }else {
            if($this->tipo_transaccion == 18)
            {
                return "$" . number_format($this->subtotal_despues_descuento_por_cotizaciones * (1 + ($this->obra->iva / 100)), 2, ".", ",");
            }
            if($this->tipo_transaccion == 50)
            {
                return "$" . number_format($this->total_por_moneda_presupuesto, 2, ".", ",");
            }
        }
    }

    public function getImpuestoConsultaProveedorFormatAttribute()
    {
        if($this->multimoneda){
            return $this->impuesto_format;
        }else {
            return "$".number_format($this->subtotal_despues_descuento_por_cotizaciones * (($this->obra->iva /100)),2,".", ",");
        }
    }

    public function getSubtotalConsultaProveedorFormatAttribute()
    {
        if($this->multimoneda){
            return $this->subtotal_format;
        }else {
            return "$".number_format($this->subtotal_por_cotizaciones,2,".", ",");
        }
    }

    public function getMonedaConsultaProveedorFormatAttribute()
    {
        if($this->multimoneda){
            return "PESOS";
        }else {
            if($this->cotizaciones()->activa()->first())
            {
                return $this->cotizaciones()->activa()->first()->moneda ? $this->cotizaciones()->activa()->first()->moneda->nombre : '';
            }
            if($this->presupuestos()->cotizadas()->first())
            {
                return $this->presupuestos()->cotizadas()->first()->moneda->nombre;
            }
        }
    }

    public function getSubtotalPorCotizacionesAttribute()
    {
        $suma = 0;
        foreach ($this->cotizaciones()->activa()->get() as $partida)
        {
            $suma += $partida->precio_total_descuento_partida;
        }
        return $suma;
    }

    public function getSubtotalDespuesDescuentoPorCotizacionesAttribute()
    {
        $suma = 0;
        foreach ($this->cotizaciones()->activa()->get() as $partida)
        {
            $suma += $partida->precio_compuesto_total;
        }
        return $suma;
    }

    public function getTotalPorMonedaPresupuestoAttribute()
    {
        $partida = $this->presupuestos()->cotizadas()->first();
        if($partida) {
            switch ($partida->IdMoneda) {
                case 1:
                    return $this->monto;
                    break;
                case 2:
                    return $this->monto / $this->TcUSD;
                    break;
                case 3:
                    return $this->monto / $this->TcEuro;
                    break;
                case 4:
                    return $this->monto / $this->TcLibra;
                    break;
            }
        }
        return 0;
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

    public  function costo(){
        return $this->belongsTo(Costo::class, 'id_costo', 'id_costo');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function antecedente()
    {
        return $this->belongsTo(Transaccion::class,"id_antecedente", "id_transaccion");
    }

    public function referente()
    {
        return $this->belongsTo(Transaccion::class,"id_referente", "id_transaccion");
    }

    public function fondoFijo()
    {
        return $this->belongsTo(Fondo::class,"id_referente", "id_fondo");
    }

    public function getSubtotalAttribute()
    {
        return $this->monto - $this->impuesto;
    }

    public function getSubtotalFormatAttribute(){
        return '$' . number_format($this->subtotal, 2, '.', ',');
    }

    public function getImpuestoFormatAttribute(){
        return '$' . number_format($this->impuesto, 2, '.', ',');
    }

    public function moneda()
    {
        return $this->hasOne(Moneda::class, 'id_moneda','id_moneda');
    }

    public function poliza()
    {
        return $this->hasOne(Poliza::class,'id_transaccion_sao', 'id_transaccion');
    }

    public function poliza_movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class,'id_transaccion_sao', 'id_transaccion');
    }

    public function polizas_historico()
    {
        return $this->hasMany(HistPoliza::class,'id_transaccion_sao', 'id_transaccion');
    }

    public function getCambio($id_moneda,$fecha)
    {
        $moneda = Moneda::find($id_moneda);
        if($moneda->tipo ==1)
        {
            return 1;
        }
        $cambio = Cambio::query()->where("id_moneda","=", $id_moneda)
            ->where("fecha", "<=",$fecha)
            ->orderBy("fecha","desc")->first();
        if($cambio)
        {
            return $cambio->cambio;
        }
        else{
            abort(500, "No hay cotización para la moneda");
        }
    }

    public function getTipoDeCambioAttribute()
    {
        if($this->id_moneda > 0)
        {
            if($this->id_moneda > 1)
            {
                $cambio = Cambio::query()->where("id_moneda","=", $this->id_moneda)
                    ->orderBy("fecha","desc")->first();
                if($cambio)
                {
                    return $cambio->cambio;
                }else{
                    return 1;
                }
            }
            else
            {
                return 1;
            }
        } else
        {
            return 1;
        }
    }

    public function getMontoPesosAttribute()
    {
        return $this->monto * $this->tipo_de_cambio;
    }

    public function getFactorConversionAttribute()
    {
        if(!$this->moneda)
        {
            return 1;
        }
        if(!$this->obra->moneda)
        {
            abort(500,"No se pudo determinar la moneda de la obra");
        }
        if($this->moneda->id_moneda == $this->obra->moneda->id_moneda)
        {
            return 1;
        }
        $tc_moneda_transaccion = $this->getCambio($this->id_moneda, $this->fecha);
        $tc_moneda_obra = $this->getCambio($this->obra->id_moneda, $this->fecha);
        return $tc_moneda_transaccion / $tc_moneda_obra;

    }

    public function desvincularPolizas()
    {
        if ($this->poliza) {
            if($this->poliza->estatus == -3){
                $this->poliza->id_transaccion_sao = null;
                $this->poliza->save();
                $movimientos = $this->poliza_movimientos()->withTrashed()->get();
                if($movimientos){
                    foreach ($movimientos as $movimiento) {
                        $movimiento->id_transaccion_sao = null;
                        $movimiento->save();
                    }
                }
                $polizas_historico = $this->polizas_historico;
                if($polizas_historico)
                {
                    foreach ($polizas_historico as $poliza_historico) {
                        $poliza_historico->id_transaccion_sao = null;
                        $poliza_historico->save();
                    }
                }
            }
        }
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(self::class,'id_transaccion', 'id_antecedente');
    }

    public function transaccionReferente()
    {
        return $this->belongsTo(self::class, 'id_referente', 'id_transaccion');
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["consulta"] = 0;

        return $datos;
    }
}
