<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/09/2019
 * Time: 06:14 PM
 */

namespace App\Models\CADECO;


class Ajuste extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 35);
        });
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function getEstatusAttribute()
    {
        if($this->estado == 0){
            return 'Registro';
        }
        if($this->estado == -1){
            return 'Cancelado';
        }
    }

    public function getTipoAttribute()
    {
        if($this->opciones == 0){
            return 'Ajuste (+)';
        }
        if($this->opciones == 1){
            return 'Ajuste (-)';
        }
        if($this->opciones == 2){
            return 'Nuevo Lote';
        }
    }
}