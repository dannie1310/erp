<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'pagos';
    protected $primaryKey = 'IdPago';

    /**
     * Relaciones
     */
    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'IdFormaPago', 'IdFormaPago');
    }
}
