<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinFacIngresoFacturaConcepto extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_fac_ingreso_factura_concepto';
    protected $primaryKey = 'idfactura_concepto';
    public $timestamps = false;
    protected $fillable = [
        'idfactura',
        'idconcepto',
        'importe'
    ];

    /**
     * Relaciones
     */
    public function tipoIngreso()
    {
        return $this->belongsTo(FinDimTipoIngreso::class,  'idconcepto','idtipo_ingreso');
    }

    public function factura()
    {
        return $this->hasOne(FinFacIngresoFactura::class,'idfactura', 'idfactura');
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Atributos
     */
    public function getImporteFormatAttribute()
    {
        return '$' . number_format($this->importe,2);
    }

    /**
     * Métodos
     */

}
