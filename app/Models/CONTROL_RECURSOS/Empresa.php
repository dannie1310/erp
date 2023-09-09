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
    public function cuentasEmpresas()
    {
        return $this->hasMany(CuentaEmpresa::class,'IdEmpresa','IdEmpresa');
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

    /**
     * Métodos
     */
}
