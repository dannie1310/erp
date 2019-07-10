<?php


namespace App\Models\CADECO;


class Solicitud extends Transaccion
{
    protected static function boot()
    {
         parent::boot();
         self::addGlobalScope('tipo',function ($query){
                return $query->where('tipo_transaccion','=',17);
         });
    }

}