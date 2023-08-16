<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class SolCheque extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solcheques';
    protected $primaryKey = 'IdSolCheques';

    /**
     * Relaciones
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'IdEmpresa', 'IdEmpresa');
    }

    public function partida()
    {
        return $this->belongsTo(PartidaSolRec::class, 'IdSolCheque', 'IdSolCheques');
    }

    /**
     * Scopes
     */

    public function scopePartidaAutorizada($query)
    {
        return $query->whereHas('partida', function ($q){
            $q->autorizada();
        });
    }

    public function scopePorSemanaAnio($query, $idsemana)
    {
        $time = SolrecSemanaAnio::where('idsemana_anio', $idsemana)->first();
        return $query->partidaAutorizada()->where('Semana', '=', $time->semana)->where('Anio', $time->anio);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
