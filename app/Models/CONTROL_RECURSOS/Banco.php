<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'bancos';
    protected $primaryKey = 'IdBanco';

    /**
     * Relaciones
     */
    public function cuentasBancarias()
    {
        return $this->hasMany(CuentaBancaria::class, 'IdBanco','IdBanco');
    }

    public function cuentasProveedores()
    {
        return $this->hasMany(CuentaProveedor::class, 'IdBanco', 'IdBanco');
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
