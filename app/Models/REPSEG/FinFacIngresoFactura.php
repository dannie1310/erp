<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinFacIngresoFactura extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_fac_ingreso_factura';
    protected $primaryKey = 'idfactura';
    public $timestamps = false;

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

    /**
     * Métodos
     */
}
