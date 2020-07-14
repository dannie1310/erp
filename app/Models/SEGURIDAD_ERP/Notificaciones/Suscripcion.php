<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/05/2020
 * Time: 02:00 AM
 */

namespace App\Models\SEGURIDAD_ERP\Notificaciones;


use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;

class Suscripcion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Notificaciones.suscripciones';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function scopeActiva($query)
    {
        return $query->where("estatus",1);
    }

}