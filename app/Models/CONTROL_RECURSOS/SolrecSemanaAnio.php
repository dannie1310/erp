<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class SolrecSemanaAnio extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solrec_semana_anio';
    protected $primaryKey = 'idsemana_anio';

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopeOrdenarPorSemana($query)
    {
        return $query->orderBy('anio','desc')->orderBy('semana', 'asc');
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
