<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class SolRecurso extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solrecursos';
    protected $primaryKey = 'IdSolRec';

    /**
     * Relaciones
     */
    public function partidas()
    {
        return $this->hasMany(PartidaSolRec::class, 'IdSolRec','IdSolRec');
    }

    /**
     * Scopes
     */
    public function scopeAutorizadas($query)
    {
        return $query->where('Estatus', 2);
    }

    public function scopePartidasAutorizadas($query)
    {
        return $query->whereHas('partidas', function ($q){
            $q->autorizadas();
        });
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
