<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 26/03/2019
 * Time: 18:45
 */

namespace App\Services\CADECO\Seguridad;

use App\Models\IGH\Usuario;
use App\Models\CADECO\Seguridad\Rol;
use App\Repositories\Repository;
use App\Traits\AuditoriaTrait;
use App\Models\CADECO\Seguridad\AuditoriaRolUser;
class RolService
{
    protected $repository;

    use AuditoriaTrait{
        getAuditoriaRolUsuario as protected traitRolUsuario;
    }
    /**
     * RolService constructor.
     * @param Rol $model
     */
    public function __construct(Rol $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    /**
     * @param $data
     * @return bool
     */

    /*public function auditoriaRolUser(){

    }*/
    public function asignacionPersonalizada($data)
    {
        $user = Usuario::query()->find($data['user_id']);
        /** @var TYPE_NAME $id_obra */
        //$id_obra = Usuario::query()->find($data['id_obra']);
        $auditoria = AuditoriaRolUser::query()->find($data['user_id']);

            foreach ($data['role_id'] as $role_id) {
                try {
                    $user->roles()->attach( $role_id  );
                    $auditoria->traitRolUsuario($data);

                } catch (\Exception $e) {
                }
            }

        return true;
    }

    public function porUsuario($data, $user_id)
    {
        $usuario = Usuario::query()->find($user_id);
        return $usuario->rolesPersonalizados()
            ->wherePivot('id_obra', '=', $data['id_obra'])
            ->get();
    }


}