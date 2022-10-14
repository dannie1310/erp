<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinDimTipoIngreso extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_dim_tipo_ingreso';
    protected $primaryKey = 'idtipo_ingreso';
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
