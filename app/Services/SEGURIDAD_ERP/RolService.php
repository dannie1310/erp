<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\AuditoriaPermisoRol;
use App\Models\SEGURIDAD_ERP\AuditoriaRolUsuario;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Models\SEGURIDAD_ERP\PermisoRol;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\Rol;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\IFTTTHandler;

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

            if ($permiso->reservado && !auth()->user()->can('asignar_permisos_reservados')) {
                throw new \Exception('No es posible asignar el permiso "' . $permiso->display_name . '" porque se trata de un permiso reservado, favor de solicitar la asignación al administrador del sistema.', 403);
            }
        }
        try {
            DB::connection('seguridad')->beginTransaction();
            $permisos_originales = $rol->permisos()->pluck('id')->toArray();
            foreach ($data['permission_id'] as $id) {
                // ASIGNACIÓN
                $permiso_rol = PermisoRol::where('permission_id', '=', $id)->where('role_id', '=', $rol->id);
                if (!in_array($id, $permisos_originales)) {
                    PermisoRol::create([
                        'permission_id' => $id,
                        'role_id' => $rol->id
                    ]);
                    AuditoriaPermisoRol::query()->create([
                        'role_id' => $data['role_id'],
                        'permission_id' => $id,
                        'action' => 'Registro'
                    ]);
                }
            }
            $permisos_actualizados = $rol->permisos()->pluck('id')->toArray();
            foreach ($permisos_originales as $id) {
                // DESASIGNACIÓN
                if (!in_array($id, $data['permission_id'] )) {
                    $permiso_rol = PermisoRol::where('permission_id', '=', $id)->where('role_id', '=', $rol->id)->delete();
                    AuditoriaPermisoRol::query()->create([
                        'role_id' => $data['role_id'],
                        'permission_id' => $id,
                        'action' => 'Eliminación'
                    ]);
                }
            }
            DB::connection('seguridad')->commit();
            return $rol;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
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
        if (Rol::query()->where('display_name', '=', $data['display_name'])->first()) {
            throw new \Exception('Ya existe un rol registrado con el nombre seleccionado.', 404);
        }
        try {
            DB::connection('seguridad')->beginTransaction();
            $rol = $this->repository->create($data);

            if (isset($data['permission_id'])) {
                foreach ($data['permission_id'] as $id) {
                    $permiso_rol = PermisoRol::where('permission_id', '=', $id)->where('role_id', '=', $rol->id);
                    if(count($permiso_rol->get()) == 0){
                        PermisoRol::create([
                            'permission_id' => $id,
                            'role_id' => $rol->id
                        ]);
                    }

                    AuditoriaPermisoRol::query()->create([
                        'role_id' => $rol->id,
                        'permission_id' => $id,
                        'action' => 'Registro'
                    ]);
                }
            }

            DB::connection('seguridad')->commit();
            return $rol;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function delete($data, $id)
    {
        $rol = $this->repository->show($id);

        if (! $rol) {
            throw new \Exception('No se encontró el rol que desea eliminar', 404);
        }

        if ($rol->usado) {
            throw new \Exception('No es posible eliminar el Rol porque se encuentra asignado a uno o varios usuarios', 403);
        } else {
            $rol->delete();
            return true;
        }
    }
}
