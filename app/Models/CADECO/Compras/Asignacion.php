<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\Transaccion;

class Asignacion extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query){
            return $query->where('tipo_transaccion','=',17);
        });
    }

    public function getEstadoFormatAttribute()
    {
        switch($this->estado){
            case 0:
                return 'Registrada';
                break;
        }
    }

}
