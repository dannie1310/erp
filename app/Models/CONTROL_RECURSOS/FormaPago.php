<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'formaspago';
    protected $primaryKey = 'IdFormaPago';

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('Estatus', 1);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
