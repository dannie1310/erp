<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/01/2020
 * Time: 06:45 PM
 */

namespace App\Models\SEGURIDAD_ERP\ControlInterno;


use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;

class UsuarioNotificacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'ControlInterno.usuarios_notificaciones';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

}