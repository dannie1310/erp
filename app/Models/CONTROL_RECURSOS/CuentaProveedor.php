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

    /**
     * Métodos
     */
}
