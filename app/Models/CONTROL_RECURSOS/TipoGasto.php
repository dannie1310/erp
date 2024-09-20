<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class TipoGasto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'tiposgasto';
    protected $primaryKey = 'IdTipoGasto';

    /**
     * Relaciones
     */
    public function cuentaIFS()
    {
        return $this->belongsTo(CuentaContableIFS::class, 'id_tipo_gasto', 'IdTipoGasto');
    }
}
