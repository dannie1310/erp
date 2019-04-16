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
use App\Models\SEGURIDAD_ERP\AuditoriaPermisoRol;
use App\Models\SEGURIDAD_ERP\Permiso;
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
    public function show($id)
    {
        return $this->repository->show($id);
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

            foreach ($data['role_id'] as $role_id) {
                try {
                    $user->roles()->attach( $role_id  );
                    AuditoriaRolUser::query()->create([
                        'user_id' => $data['user_id'],
                        'role_id' => $role_id,
                        'action' => 'Registro'
                    ]);

                } catch (\Exception $e) {
                }
            }

        return true;
    }
    public function desasignacionPersonalizada($data)
    {
        $user = Usuario::query()->find($data['user_id']);

        foreach ($data['role_id'] as $role_id) {
                try {
                    $user->roles()
                        ->wherePivot('role_id', '=', $role_id)
                        ->detach();
                    AuditoriaRolUser::query()->create([
                        'user_id' => $data['user_id'],
                        'role_id' => $role_id,
                        'action' => 'Eliminación'
                    ]);
                } catch (\Exception $e) {}
            }
        return true;
    }

    public function asignacionPermisos($data)
    {
        $rol = $this->repository->show($data['role_id']);

        foreach ($data['permission_id'] as $perm) {
            $permiso = Permiso::find($perm);

            if($permiso->reservado &&  ! auth()->user()->can('asignar_permisos_reservados')) {
                throw new \Exception('No es posible asignar el permiso "' . $permiso->display_name. '" porque se trata de un permiso reservado, favor de solicitar la asignación al administrador del sistema.', 403);
            }
        }
        $permisos_originales = $rol->permisos()->pluck('id')->toArray();

        foreach ($data['permission_id'] as $id) {
            // ASIGNACIÓN
            if (! in_array($id, $permisos_originales)) {
                \App\Models\CADECO\Seguridad\AuditoriaPermisoRol::query()->create([
                    'role_id' => $data['role_id'],
                    'permission_id' => $id,
                    'action' => 'Registro'
                ]);
            }
        }

        $rol->permisos()->detach($rol->permisos()->pluck('id')->toArray());
        $rol->permisos()->sync($data['permission_id'], false);
        $permisos_actualizados = $rol->permisos;

        foreach ($permisos_originales as $id) {
            // DESASIGNACIÓN
            if (! in_array($id, $permisos_actualizados)) {
                \App\Models\CADECO\Seguridad\AuditoriaPermisoRol::query()->create([
                    'role_id' => $data['role_id'],
                    'permission_id' => $id,
                    'action' => 'Eliminación'
                ]);
            }
        }
        return true;
    }

    public function porUsuario($data, $user_id)
    {
        $usuario = Usuario::query()->find($user_id);
        return $usuario->rolesPersonalizados()
            ->get();
    }
    public function store($data)
    {
        $rol = $this->repository->create($data);

        if (isset($data['permission_id'])) {
            $rol->permisos()->attach($data['permission_id']);
        }

        return $rol;
    }


}