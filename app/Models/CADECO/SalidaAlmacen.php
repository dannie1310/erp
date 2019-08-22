<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Transaccion;

class SalidaAlmacen extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 34)
                ->where('opciones', '=', 1);
        });
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class,'id_almacen','id_almacen');
    }

}