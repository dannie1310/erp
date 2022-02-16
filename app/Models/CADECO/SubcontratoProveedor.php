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

class SubcontratoProveedor extends Transaccion
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

    public function getAnticipoFormatAttribute()
    {
        return number_format(abs($this->anticipo), 2) . '%';
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
}
