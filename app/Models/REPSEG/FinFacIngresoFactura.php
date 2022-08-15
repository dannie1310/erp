<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinFacIngresoFactura extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_fac_ingreso_factura';
    protected $primaryKey = 'idfactura';
    public $timestamps = false;
    protected $fillable = [
        'estado',
        'usuario_cancelo',
        'motivo_cancelacion',
        'fecha_cancelacion'
    ];

    /**
     * Relaciones
     */
    public function proyecto()
    {
        return $this->belongsTo(GrlProyecto::class, 'idproyecto', 'idproyecto');
    }

    public function empresa()
    {
        return $this->belongsTo(FinDimIngresoEmpresa::class, 'idempresa', 'idempresa');
    }

    public function cliente()
    {
        return $this->belongsTo(FinDimIngresoCliente::class, 'idcliente', 'idcliente');
    }

    public function moneda()
    {
        return $this->belongsTo(GrlMoneda::class, 'idmoneda', 'idmoneda');
    }

    public function conceptos()
    {
        return $this->hasMany(FinFacIngresoFacturaConcepto::class,  'idfactura','idfactura');
    }

    public function partidas()
    {
        return $this->hasMany(FinFacIngresoFacturaDetalle::class, 'idfactura', 'idfactura');
    }

    public function partidasSinTotales()
    {
        return $this->hasMany(FinFacIngresoFacturaDetalle::class, 'idfactura', 'idfactura')->whereNotIn('idpartida', [15,16,17]);
    }


    /**
     * Scopes
     */


    /**
     * Atributos
     */
    public function getNombreProyectoAttribute()
    {
        try{
            return $this->proyecto->proyecto;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreEmpresaAttribute()
    {
        try{
            return $this->empresa->empresa;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreMonedaAttribute()
    {
        try{
            return $this->moneda->moneda;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreClienteAttribute()
    {
        try{
            return $this->cliente->cliente;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaFiFormatAttribute()
    {
        $date = date_create($this->fi_cubre);
        return date_format($date,"d/m/Y");
    }

    public function getFechaFfFormatAttribute()
    {
        $date = date_create($this->ff_cubre);
        return date_format($date,"d/m/Y");
    }

    public function getFechaCobroFormatAttribute()
    {
        $date = date_create($this->fecha_cobro);
        return date_format($date,"d/m/Y");
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format($this->importe,2);
    }

    public function getEstadoDescripcionAttribute()
    {
        switch ($this->estado)
        {
            case 1:
                return 'Registrada';
                break;

            case 2:
                return 'Pagada';
                break;

            case 3:
                return 'Cancelada';
                break;
            default:
                return 'Desconocido';
                break;
        }
    }

    public function getEstadoColorAttribute()
    {
        switch ($this->estado) {
            case 1:
                return '#f39c12';
                break;

            case 2:
                return '#00a65a';
                break;

            case 3:
                return '#e50c25';
                break;
            default:
                return '#d2d6de';
                break;
        }
    }

    public function getSubtotalFormatAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 15)->first()->total_format;

        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getIvaFormatAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 16)->first()->total_format;

        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getTotalFormatAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 17)->first()->total_format;

        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Métodos
     */
    public function cancelar($motivo)
    {
        try {
            DB::connection('repseg')->beginTransaction();
            $this->update([
                'estado' => 3,
                'usuario_cancelo' => auth()->id(),
                'motivo_cancelacion' => $motivo,
                'fecha_cancelacion' => date('Y-m-d H:i:s')
            ]);
            DB::connection('repseg')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('repseg')->rollBack();
            throw $e;
        }
    }
}
