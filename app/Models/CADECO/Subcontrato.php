<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 03:55 PM
 */

namespace App\Models\CADECO;
use App\Facades\Context;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use Illuminate\Support\Facades\DB;

class Subcontrato extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'id_empresa',
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
    ];
    protected $with = array( 'estimacion');
    public $usuario_registra = 1;

    public $searchable = [
        'numero_folio',
        'referencia'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 51)
                ->where('opciones', '=', 2)
                ->whereIn('estado', [0, 1]);
        });
        self::creating(function ($subcontrato) {

            $subcontrato->tipo_transaccion = 51;
            $subcontrato->opciones = 2;

        });
        self::created(function ($subcontrato) {
            if ($subcontrato->retencion > 0) {
                $subcontrato->generaFondoGarantia();
            }
        });
    }

    public function estimacion(){
        return $this->hasMany(Estimacion::class,'id_antecedente','id_transaccion');
    }

    public function estimaciones()
    {
        return $this->hasMany(Estimacion::class, 'id_antecedente', 'id_transaccion');
    }

    public function fondo_garantia()
    {
        return $this->hasOne(FondoGarantia::class, 'id_subcontrato', 'id_transaccion');
    }

    public function moneda()
    {
        return $this->hasOne(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function generaFondoGarantia()
    {
        if(is_null($this->fondo_garantia))
        {
            if ($this->retencion > 0) {
                $fondo_garantia = new FondoGarantia();
                $fondo_garantia->id_subcontrato = $this->id_transaccion;
                $fondo_garantia->usuario_registra = $this->usuario_registra;
                $fondo_garantia->save();
                $this->refresh();
            } else {
                throw New \Exception('El subcontrato no tiene establecido un porcentaje de retención de fondo de garantía, el fondo de garantía no puede generarse');
            }
        }
    }

    public function getSubtotalAttribute()
    {
        return $this->monto-$this->impuesto;
    }

    public function scopeSinFondo($query)
    {
        return $query->whereDoesntHave('fondo_garantia');
    }

    public function scopeConFondo($query)
    {
        return $query->whereHas('fondo_garantia');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function pago_anticipado(){
        return $this->hasOne(SolicitudPagoAnticipado::class,'id_antecedente', 'id_transaccion');
    }

    public function getNombre(){
        return 'SUBCONTRATO';
    }

    public function partidas_facturadas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_antecedente', 'id_transaccion');
    }

    public function getMontoFacturadoEstimacionAttribute()
    {
        return round(FacturaPartida::query()->whereIn('id_antecedente', $this->estimaciones()->pluck('id_transaccion'))->sum('importe'));
    }

    public function getMontoFacturadoSubcontratoAttribute()
    {
        return round($this->partidas_facturadas()->sum('importe'),2);
    }

    public function getMontoPagoAnticipadoAttribute()
    {
        return round($this->pago_anticipado()->where('estado', '>=',0)->sum('monto'), 2);
    }

    public function getMontoDisponibleAttribute()
    {
        return round($this->subtotal - ($this->montoFacturadoEstimacion + $this->montoFacturadoSubcontrato + $this->MontoPagoAnticipado), 2);
    }

    public function scopeSubcontratosDisponible($query)
    {
        $transacciones = DB::connection('cadeco')->select(DB::raw("          
                            select s.id_transaccion from transacciones s
                            left join (select SUM(monto) as solicitado, id_antecedente as id from  transacciones
                            where tipo_transaccion = 72 and opciones = 327681 and estado >= 0 and 
                            id_obra = ".Context::getIdObra()." group by id_antecedente)
                            as sol on sol.id = s.id_transaccion 
                            left join 
                            (select SUM(i.importe) as suma_anticipo, i.id_antecedente as id from items i
                            join transacciones factura on factura.id_transaccion = i.id_transaccion
                            join transacciones sub on sub.id_transaccion = i.id_antecedente 
                            where factura.tipo_transaccion = 65 and factura.estado >= 0 and
                            sub.tipo_transaccion = 51 and sub.opciones = 2 and sub.estado >= 0 and sub.id_obra = ".Context::getIdObra()."
                            group by i.id_antecedente)
                            as factura_anticipo on factura_anticipo.id = s.id_transaccion
                            left join (
                            select SUM(i.importe) as suma_e, e.id_antecedente as id  from items i
                            join transacciones f on f.id_transaccion = i.id_transaccion
                            join transacciones e on e.id_transaccion = i.id_antecedente 
                            where f.tipo_transaccion = 65 and f.estado >= 0 and e.tipo_transaccion = 52 and e.estado >= 0 and f.id_obra =  ".Context::getIdObra()."
                            group by e.id_antecedente )
                            as facturado_e on facturado_e.id = s.id_transaccion
                            where s.tipo_transaccion = 51 and s.estado >= 0 and  s.id_obra =  ".Context::getIdObra()."  and s.opciones = 2
                            and (ROUND(s.monto - s.impuesto, 2) - ROUND((ISNULL(sol.solicitado,0) + ISNULL(factura_anticipo.suma_anticipo, 0) + ISNULL(facturado_e.suma_e, 0)),2)) > 0 order by s.id_transaccion;"));

        $transacciones = json_decode(json_encode($transacciones), true);

        return $query->whereIn('id_transaccion', $transacciones);
    }
}