<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CuentaProveedor extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'cuentasproveedores';
    protected $primaryKey = 'IdCuenta';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('Estatus', 1);
        });
    }

    /**
     * Relaciones
     */
    public function banco()
    {
        return $this->belongsTo(Banco::class,'IdBanco','IdBanco');
    }

    /**
     * Scopes
     */

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

    public function getBancoCuentaAttribute()
    {
        try {
            return $this->banco->Banco;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getBancoCveAttribute()
    {
        try {
            return $this->banco->CVEBanco;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Métodos
     */
}
