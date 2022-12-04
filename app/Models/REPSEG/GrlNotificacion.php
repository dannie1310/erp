<?php

namespace App\Models\REPSEG;

use Illuminate\Database\Eloquent\Model;

class GrlNotificacion extends Model
{
    protected $connection = 'repseg';
    protected $table = 'grl_notificacion';
    protected $primaryKey = 'idnotificacion';
    public $timestamps = false;

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopeSeccion($query, $value)
    {
        return $query->where('idseccion', $value);
    }

    public function scopeProyecto($query, $value)
    {
        return $query->where('idproyecto', $value);
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', '=', 1);
    }


    /**
     * Attributos
     */

    /**
     * Métodos
     */
}
