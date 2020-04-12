<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 03:55 PM
 */

namespace App\Models\CADECO;
use App\Facades\Context;
use App\Models\CADECO\Subcontratos\ClasificacionSubcontrato;
use App\Models\CADECO\Subcontratos\Subcontratos;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
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
        self::addGlobalScope('tipo', function ($query) {
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

    public function clasificacionSubcontrato()
    {
        return $this->belongsTo(ClasificacionSubcontrato::class, 'id_transaccion');
    }

    public function estimaciones()
    {
        return $this->hasMany(Estimacion::class, 'id_antecedente', 'id_transaccion');
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

    public function pago_anticipado()
    {
        return $this->hasOne(SolicitudPagoAnticipado::class, 'id_antecedente', 'id_transaccion');
    }

    public function partidas_facturadas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_antecedente', 'id_transaccion');
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
            $items [$partida->nivel] = $partida->partidasEstimadas($id_estimacion, $this->id_antecedente);
        }
        $respuesta = array(
            'folio' => $this->numero_folio_format,
            'referencia' => $this->referencia,
            'partidas' => $items
        );
        return $respuesta;
    }

    public function getAcumuladoRetencionAnterioresAttribute()
    {
        $acumulado = 0;
        foreach ($this->estimaciones as $estimacion) {
            $acumulado += $estimacion->retenciones->sum('importe');
        }
        return $acumulado;
    }

    public function getAcumuladoLiberacionAnterioresAttribute()
    {
        $acumulado = 0;
        foreach ($this->estimaciones as $estimacion) {
            $acumulado += $estimacion->liberaciones->sum('importe');
        }
        return $acumulado;
    }

    public function getImporteFondoGarantiaAttribute(){
        return ($this->monto - $this->impuesto) * $this->retencion / 100;
    }
}
