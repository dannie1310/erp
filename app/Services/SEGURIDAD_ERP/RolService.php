<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\AuditoriaPermisoRol;
use App\Models\SEGURIDAD_ERP\AuditoriaRolUsuario;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\Rol;
use App\Repositories\Repository;

class RolService
{
    /**
     * @var Repository
     */
    protected $repository;

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

    public function asignacionMasiva($data)
    {
        $user = Usuario::query()->find($data['user_id']);

        foreach ($data['id_proyecto'] as $datum) {
            $database = explode('-', $datum)[0];
            $proyecto = Proyecto::query()->withoutGlobalScopes()->where('base_datos', '=', $database)->first();
            $id_obra =explode('-', $datum)[1];

            foreach ($data['role_id'] as $role_id) {
                try {
                    $user->roles()->attach([$role_id => ['id_obra' => $id_obra, 'id_proyecto' => $proyecto->getKey()]]);
                    AuditoriaRolUsuario::query()->create([
                        'user_id' => $data['user_id'],
                        'role_id' => $role_id,
                        'id_proyecto' => $proyecto->getKey(),
                        'id_obra' => $id_obra,
                        'action' => 'Registro'
                    ]);
                } catch (\Exception $e) {}
            }
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
                AuditoriaPermisoRol::query()->create([
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
                AuditoriaPermisoRol::query()->create([
                    'role_id' => $data['role_id'],
                    'permission_id' => $id,
                    'action' => 'Eliminación'
                ]);
            }
        }

        return true;
    }

    public function desasignacionMasiva($data)
    {
        $user = Usuario::query()->find($data['user_id']);

        foreach ($data['id_proyecto'] as $datum) {
            $database = explode('-', $datum)[0];
            $proyecto = Proyecto::query()->withoutGlobalScopes()->where('base_datos', '=', $database)->first();
            $id_obra =explode('-', $datum)[1];

            foreach ($data['role_id'] as $role_id) {
                try {
                    $user->roles()
                        ->wherePivot('role_id', '=', $role_id)
                        ->wherePivot('id_obra', '=', $id_obra)
                        ->wherePivot('id_proyecto', '=', $proyecto->getKey())
                        ->detach();

                    AuditoriaRolUsuario::query()->create([
                        'user_id' => $data['user_id'],
                        'role_id' => $role_id,
                        'id_proyecto' => $proyecto->getKey(),
                        'id_obra' => $id_obra,
                        'action' => 'Eliminación'
                    ]);
                } catch (\Exception $e) {}
            }
        }

        return true;
    }

    public function porUsuario($data, $user_id)
    {
        $usuario = Usuario::query()->find($user_id);
        $proyecto = Proyecto::query()->withoutGlobalScopes()->where('base_datos', '=', $data['base_datos'])->first();
        return $usuario->rolesGlobales()
            ->wherePivot('id_proyecto', '=', $proyecto->getKey())
            ->wherePivot('id_obra', '=', $data['id_obra'])
            ->get();
    }

    public function store($data)
    {
        $rol = $this->repository->create($data);

        if (isset($data['permission_id'])) {
           $rol->permisos()->attach($data['permission_id']);

           foreach ($data['permission_id'] as $id) {
               AuditoriaPermisoRol::query()->create([
                   'role_id' => $rol->id,
                   'permission_id' => $id,
                   'action' => 'Registro'
               ]);
           }
        }

        return $rol;
    }
}