<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\Banco;
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

    public function scopeVerCotizaciones($query)
    {

//        $cotizacion = Transaccion::query()->where('tipo_transaccion','=',18)->get()->toArray();
//        dd($cotizacion[0]['cumplimiento']);
//        $bancos = array_column(Banco::query()->select('id_ctg_bancos')->where('id_ctg_bancos', '>', 0)->get()->toArray(),'id_ctg_bancos');
////        dd($bancos);
//        $cotizacion = array_column(Transaccion::query()->where('tipo_transaccion','=',18)->get()->toArray(),'id_transaccion');
////        dd($cotizacion);
//        return $query->whereNotIn('id_antecedente',$cotizacion);

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
