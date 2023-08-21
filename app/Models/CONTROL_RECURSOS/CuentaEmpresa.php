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
    public function getNumeroCuentaAttribute()
    {
        try {
            return $this->cuenta->Cuenta;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getBancoDescripcionAttribute()
    {
        try {
            return $this->cuenta->banco_cuenta;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getTipoCuentaAttribute()
    {
        try {
            return $this->cuenta->tipo->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getIdBancoAttribute()
    {
        try {
            return $this->cuenta->IdBanco;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Métodos
     */
}
