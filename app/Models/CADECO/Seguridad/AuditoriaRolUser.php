<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 04/04/2019
 * Time: 19:28
 */

namespace App\Models\CADECO\Seguridad;

use App\Facades\Context;
use App\Models\CADECO\Seguridad\Rol;
use Illuminate\Database\Eloquent\Model;

class AuditoriaRolUser extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Seguridad.auditoria_role_user';

    protected $fillable = [
        'usuario_registro',
        'action'
    ];


    public function roles()
    {
        return $this->belongsTo(Rol::class, 'role_id');
    }
}