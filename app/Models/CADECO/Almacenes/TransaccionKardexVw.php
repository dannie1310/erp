<?php

namespace App\Models\CADECO\Almacenes;

use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class TransaccionKardexVw extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Almacenes.vwTransaccionesKardex';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    /**
     * Relaciones
     */

    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */
    public function getHistorico()
    {
        dd($this);
    }
}
