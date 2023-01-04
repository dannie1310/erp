<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class GrlProyecto extends Model
{
    protected $connection = 'repseg';
    protected $table = 'grl_proyecto';
    protected $primaryKey = 'idproyecto';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function tipo()
    {
        return $this->belongsTo(GrlProyectoTipo::class, 'idproyecto_tipo','idproyecto_tipo');
    }

    /**
     * Scopes
     */
    public function scopePorTipo($query)
    {
        return $query->where('estado', 1)->where('idproyecto_clasificacion', 4)->orderBy('idproyecto_tipo', 'ASC');
    }

    /**
     * Attributos
     */

    /**
     * Métodos
     */
}
