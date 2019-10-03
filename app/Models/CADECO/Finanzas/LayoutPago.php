<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:51 PM
 */

namespace App\Models\CADECO\Finanzas;

use App\Models\IGH\Usuario;
use App\Models\CADECO\Finanzas\CtgEstadoLayoutPago;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use Illuminate\Database\Eloquent\Model;

class LayoutPago extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.layout_pagos';
    public $timestamps = false;

    public function partidas()
    {
        return $this->hasMany(LayoutPagoPartida::class,'id_layout_pagos', 'id');
    }

    public function estadoLayout()
    {
        return $this->hasOne(CtgEstadoLayoutPago::class, 'estado', 'estado' );
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,  'id_usuario_carga', 'idusuario');
    }

    public  function usuarioAutorizo()
    {
        return $this->belongsTo(Usuario::class,  'id_usuario_autorizo', 'idusuario');
    }
}
