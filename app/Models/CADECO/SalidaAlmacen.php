<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Transaccion;

class SalidaAlmacen extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 34);
        });
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class,'id_almacen','id_almacen');
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
        }
    }

    public function getOperacionAttribute()
    {
        switch ($this->opciones){
            case 1 :
                return 'Salida de Almacén';
                break;
            case 65537 :
                return 'Transferencia';
                break;
        }
    }

}