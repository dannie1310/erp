<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class PartidaSolRec extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'partidassolrec';
    protected $primaryKey = 'IdPartidaSolRec';

    /**
     * Relaciones
     */
    public function cheque()
    {
        return $this->belongsTo(SolCheque::class, 'IdSolCheque','IdSolCheques');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
    }

    /**
     * Scopes
     */
    public function scopeAutorizada($query)
    {
        return $query->where('Estatus', 2);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
