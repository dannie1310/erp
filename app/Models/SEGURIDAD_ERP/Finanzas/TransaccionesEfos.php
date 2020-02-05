<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;

use App\Models\CADECO\Empresa;
use App\Models\CADECO\Transaccion;

class TransaccionesEfos extends Transaccion
{
        
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->has('efoTransaccion');
        });
    }

    public function efoTransaccion()
    {
        return $this->hasManyThrough(CtgEfos::class, Empresa::class, 'id_empresa', 'rfc', 'id_empresa', 'rfc');
    }

      
}