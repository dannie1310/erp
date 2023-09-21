<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CuentaEmpresa extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'vw_cuentas_bancarias_empresa';


    /**
     * Relaciones
     */
    public function cuenta()
    {
        return $this->belongsTo(CuentaBancaria::class,'IdCuentaBancaria','IdCuentaBancaria');
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class,'IdBanco','IdBanco');
    }

    public function tipoCuenta()
    {
        return $this->belongsTo(CuentaBancariaTipo::class, 'IdTipoCuenta', 'IdTipoCuenta');
    }

    public function moneda()
    {
        return $this->belongsTo(CtgMoneda::class, 'id', 'IdMoneda');
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
            return $this->Cuenta;
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
            return $this->IdBanco;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Métodos
     */
}
