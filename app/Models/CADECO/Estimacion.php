<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 04:24 PM
 */

namespace App\Models\CADECO;
use App\Facades\Context;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Models\CADECO\SubcontratosEstimaciones\FolioPorSubcontrato;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;
use App\Models\CADECO\SubcontratosFG\RetencionFondoGarantia;
use Illuminate\Support\Facades\DB;

class Estimacion extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion'
    ];

    public $searchable = [
        'numero_folio',
        'observaciones',
        'subcontrato.empresa.razon_social',
        'subcontrato.referencia'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 52);
        });

        self::creating(function ($estimacion) {
            $subcontrato = Subcontrato::query()->find($estimacion->id_antecedente);

            $estimacion->tipo_transaccion = 52;
            $estimacion->id_empresa = $subcontrato->id_empresa;
            $estimacion->id_moneda = $subcontrato->id_moneda;
            $estimacion->saldo = $estimacion->monto;
            $estimacion->retencion = $subcontrato->retencion;
            $estimacion->fecha = date('Y-m-d');
            $estimacion->numero_folio = self::calcularFolio();
        });

        self::created(function ($estimacion) {
            if ($estimacion->retencion > 0) {
                $estimacion->generaRetencion();
            }
            $estimacion->creaSubcontratoEstimacion();
        });
    }

    /**
     * Relaciones Eloquent
     */
    public function retencion_fondo_garantia()
    {
        return $this->hasOne(RetencionFondoGarantia::class,'id_estimacion','id_transaccion');
    }

    public function subcontrato()
    {
        # return $this->belongsTo(Subcontrato::class,'id_transaccion', 'id_antecedente');
        return $this->hasOne(Subcontrato::class, 'id_transaccion', 'id_antecedente');
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'id_transaccion', 'id_transaccion');
    }

    public function liberaciones()
    {
        return $this->hasMany(Liberacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function subcontratoEstimacion()
    {
        return $this->hasOne(\App\Models\CADECO\SubcontratosEstimaciones\Estimacion::class, 'IDEstimacion', 'id_transaccion');
    }

    public function retenciones()
    {
        return $this->hasMany(Retencion::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Acciones
     */
    public function creaSubcontratoEstimacion()
    {
        \App\Models\CADECO\SubcontratosEstimaciones\Estimacion::query()->create([
            'IDEstimacion' => $this->id_transaccion,
			'NumeroFolioConsecutivo' => $this->generaFolioConsecutivo()
        ]);
    }

    private static function calcularFolio()
    {
        $est = self::orderBy('numero_folio', 'DESC')->first();
        return $est ? $est->numero_folio + 1 : 1;
    }

    private function generaFolioConsecutivo()
    {
        $folio = FolioPorSubcontrato::query()
            ->where('IDSubcontrato', '=', $this->id_antecedente)
            ->first();

        if($folio) {
            $folio->UltimoFolio += 1;
            $folio->save();
        } else {
            $folio = FolioPorSubcontrato::query()->create([
                'IDSubcontrato' => $this->id_antecedente
            ]);
        }
        return $folio->UltimoFolio;
    }

    public function calculaImportes()
    {
        // Calculo del importe de amortizacion de anticipo
        $amortizacion_anticipo = ($this->subcontrato->anticipo / 100) * $this->sumaImportes;

        // Calculo del importe de fondo de garantia
        $fondo_garantia = ($this->subcontrato->retencion / 100) * $this->sumaImportes;

        // Calculo del subtotal
        $subtotal = $this->sumaImportes;

        // Descuento de amortizacion de anticipo antes de iva
        $subtotal -= $amortizacion_anticipo;

        // Se calcula el iva y total
        $iva = $subtotal * ($this->subcontrato->impuesto / ($this->subcontrato->monto - $this->subcontrato->impuesto));
        $total = $subtotal + $iva;

        $this->impuesto = $iva;
        $this->monto = $total;
        $this->saldo = $total;
        $this->retencion = ($this->subcontrato->retencion / 100) * 100;
        $this->anticipo = ($this->subcontrato->anticipo / 100);
        $this->save();

        $subcontratoEstimacion = \App\Models\CADECO\SubcontratosEstimaciones\Estimacion::query()
            ->where('IDEstimacion', '=', $this->id_transaccion)
            ->first();

        $subcontratoEstimacion->PorcentajeFondoGarantia = ($this->subcontrato->retencion / 100);
        $subcontratoEstimacion-> ImporteFondoGarantia = $fondo_garantia;
        $subcontratoEstimacion->save();
    }

    public function generaRetencion()
    {
        if(is_null($this->retencion_fondo_garantia))
        {
            if ($this->retencion > 0) {
                $retencion_fondo_garantia = new RetencionFondoGarantia();
                $retencion_fondo_garantia->id_estimacion = $this->id_transaccion;
                $retencion_fondo_garantia->importe = $this->importe_retencion;
                $retencion_fondo_garantia->usuario_registra = $this->usuario_registra;
                $retencion_fondo_garantia->save();
                $this->refresh();
            } else {
                throw New \Exception('La estimación no tiene establecido un porcentaje de retención de fondo de garantía, la retención no puede generarse');
            }
        }
    }

    public function aprobar()
    {
        if ($this->sumaImportes == 0) {
            throw new \Exception('La estimacion no tiene importe');
        }
        if ($this->estado > 0) {
            throw new \Exception('La estimacion se encuentra aprobada.');
        }

        $fecha = date("d/m/Y H:i");
        $usuario = auth()->user()->usuario;
        $this->comentario = $this->comentario . "A;{$fecha};{$usuario}|";
        $this->impreso = 1;
        $this->saldo = $this->monto;
        $this->save();

        DB::connection('cadeco')->update("EXEC [dbo].[sp_aprobar_transaccion] {$this->id_transaccion}");

        return $this;
    }

    public function revertirAprobacion()
    {
        if ($this->estado == 2) {
            throw new \Exception('La transacción no puede modificarse por que esta aprobada o revisada.');
        }

        DB::connection('cadeco')->update("EXEC [dbo].[sp_revertir_transaccion] {$this->id_transaccion}");
        
        return $this;
    }

    public function getImporteRetencionAttribute()
    {
        return $this->monto*$this->retencion /100;
    }

    public function getSumaImportesAttribute()
    {
        return $this->items()->sum('importe');
    }

    public function getAmortizacionPendienteAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();
        return $this->subcontrato->anticipo_monto - $estimaciones_anteriores->sum('monto_anticipo_aplicado') - $this->monto_anticipo_aplicado;
    }

    public function getAmortizacionPendienteAnteriorAttribute()
    {
        $estimacion_anterior = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->orderBy('id_transaccion', 'DESC')->first();
        if ($estimacion_anterior) {
            return $estimacion_anterior->amortizacion_pendiente;
        } else {
            return 0;
        }
    }

    public function getMontoAnticipoAplicadoAttribute()
    {
        return $this->suma_importes * ($this->anticipo / 100);
    }

    public function getRetenidoAnteriorAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();

        $sumatoria = 0;
        foreach ($estimaciones_anteriores as $estimacion) {
            $sumatoria += $estimacion->SumMontoRetencion;
        }
        return $sumatoria;
    }

    public function getRetenidoOrigenAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();

        $sumatoria = 0;
        foreach ($estimaciones_anteriores as $estimacion) {
            $sumatoria += $estimacion->SumMontoRetencion;
        }
        return $sumatoria + $this->SumMontoRetencion;
    }

    public function getMontoAPagarAttribute()
    {
        return (
            $this->monto

            - ($this->subcontratoEstimacion ? $this->subcontratoEstimacion->ImporteFondoGarantia : 0)
            - (!in_array(Context::getDatabase(),['SAO1814_TERMINAL_NAICM', 'SAO1814_DEV_TERMINAL_NAICM']) ? $this->descuentos->sum('importe') : 0)
            - $this->retenciones->sum('importe')
            - $this->IVARetenido
            + $this->liberaciones->sum('importe')
            + ($this->subcontratoEstimacion ? $this->subcontratoEstimacion->ImporteAnticipoLiberar : 0)
        );
    }
}