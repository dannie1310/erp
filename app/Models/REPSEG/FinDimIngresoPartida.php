<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinDimIngresoPartida extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_dim_ingreso_partidas';
    protected $primaryKey = 'idpartida';
    public $timestamps = false;

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Attributos
     */

    /**
     * Métodos
     */
}
