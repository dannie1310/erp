<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class TipoGastoComp extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'tiposgastocomp';
    protected $primaryKey = 'IdTipoGastoComp';

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopePorEstados($query, $estados)
    {
        return $query->whereIn('Estatus', [$estados]);
    }
}
