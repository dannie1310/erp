<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class PartidaPago extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'partidaspagos';
    protected $primaryKey = 'IdPartidaPago';

    /**
     * Relaciones
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class, 'IdPago', 'IdPago');
    }
}
