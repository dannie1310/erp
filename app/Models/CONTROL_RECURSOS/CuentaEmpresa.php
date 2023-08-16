<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CuentaEmpresa extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'cuenta_empresa';


    /**
     * Relaciones
     */
    public function cuenta()
    {
        return $this->belongsTo(CuentaBancaria::class,'IdCuentaBancaria','IdCuentaBancaria');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
