<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:51 PM
 */

namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class LayoutPago extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.layout_pagos';
    public $timestamps = false;

    public function partidas()
    {
        return $this->hasMany(LayoutPagoPartida::class, 'i_layout_pagos', 'id');
    }

    public function estado()
    {
        return $this->belongsTo(CtgEstadoLayoutPago::class, 'estado', 'estado');
    }
}