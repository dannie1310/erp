<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 05/04/2019
 * Time: 18:15
 */

namespace App\Traits;

use Illuminate\Http\Request;

trait AuditoriaTrait
{
    private function getAuditoriaRolUsuario(){
        $item = $this->service->auditoriaRolUser(/*$request->all()*/);
        return $this->respondWithItem($item);
    }

    private function getAuditoriaPermisoRol(){

    }
}