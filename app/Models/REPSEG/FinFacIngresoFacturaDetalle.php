<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinFacIngresoFacturaDetalle extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_fac_ingreso_factura_detalle';
    protected $primaryKey = 'idfactura_detalle';
    public $timestamps = false;
    protected $fillable = [
        'idfactura',
        'idpartida',
        'antes_iva',
        'total',
        'timestamp'
    ];

    /**
     * Relaciones
     */
    public function partida()
    {
        return $this->belongsTo(FinDimIngresoPartida::class, 'idpartida','idpartida');
    }

    /**
     * Atributos
     */
    public function getTotalFormatAttribute()
    {
        return '$' . number_format($this->total,2);
    }

    public function getTotalSinSignoFormatAttribute()
    {
        return number_format($this->total,2);
    }

    /**
     * Scopes
     */



    /**
     * Métodos
     */
}
