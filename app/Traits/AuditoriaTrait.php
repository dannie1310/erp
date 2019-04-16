<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 05/04/2019
 * Time: 18:15
 */

namespace App\Traits;

use App\Models\CADECO\Seguridad\AuditoriaRolUser;
use App\Models\CADECO\Seguridad\Rol;
use App\Models\CADECO\Usuario;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

trait AuditoriaTrait
{
    private function getAuditoriaRolUsuario($data){
        $user = Usuario::query()->find($data['user_id']);
        $roles = Rol::query()->find($data['role_id']);
        $auditoria = AuditoriaRolUser::query()->find($data['user_id']);
        foreach ($data['role_id']as $role_id){
            try{
                $auditoria->roles()->attach([$role_id => ['user_id' => $user]]);
            }catch (\Exception $e){}
        }
    }

    private function getAuditoriaPermisoRol(){

    }
}