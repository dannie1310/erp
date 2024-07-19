<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'empresas';
    protected $primaryKey = 'IdEmpresa';

    /**
     * Relaciones
     */
    public function cuentasEmpresa()
    {
        return $this->hasMany(CuentaEmpresa::class,'IdEmpresa','IdEmpresa');
    }

    public function cuentasPagadorasEmpresa()
    {
        return $this->hasMany(CuentaPagadoraEmpresa::class,'IdEmpresa','IdEmpresa');
    }

    public function cuentasPagadorasSantanderEmpresa()
    {
        return $this->hasMany(CuentaPagadoraSantanderEmpresa::class,'IdEmpresa','IdEmpresa');
    }

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
    public function getRfcSinGuionesAttribute()
    {
        return str_replace("-", "", $this->RFC);
    }

    /**
     * Métodos
     */
}
