<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinFacIngresoFacturaConcepto extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_fac_ingreso_factura_concepto';
    protected $primaryKey = 'idfactura_concepto';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function tipoIngreso()
    {
        return $this->belongsTo(FinDimTipoIngreso::class,  'idconcepto','idtipo_ingreso');
    }

    /**
     * Scopes
     */

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
