<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:53 PM
 */

namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoLayoutPago extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.ctg_estado_layout_pagos';
    public $timestamps = false;
}