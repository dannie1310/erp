<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'cuentasbancarias';
    protected $primaryKey = 'IdCuentaBancaria';

    /**
     * Relaciones
     */
    public function banco()
    {
        return $this->belongsTo(Banco::class,'IdBanco','IdBanco');
    }

    public function tipo()
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
    public function scopeActiva($query)
    {
        return $query->where('Estatus', 1);
    }

    /**
     * Atributos
     */
    public function getTipoCuentaAttribute()
    {
        try {
            return $this->tipo->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getMonedaDescripcionAttribute()
    {
        try {
            return $this->moneda->moneda;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getBancoCuentaAttribute()
    {
        try {
            return $this->banco->Banco;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Métodos
     */
}
