<?php


namespace App\Services\SEGURIDAD_ERP;

use App\Models\IGH\Usuario;
use App\Models\CADECO\Seguridad\Rol;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Repositories\Repository;

class PermisoService
{
    protected $repository;


    public function __construct(Permiso $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data){
        return $this->repository->all($data);
    }

    public function asignacionPersonalizada($data)
    {
        $rol = Rol::query()->find($data['role_id']);

        foreach ($data['permission_id'] as $permission_id) {
            try {
                $rol->roles()->belongsTo( $permission_id  );
            } catch (\Exception $e) {
            }
        }

        return true;
    }

    public function porRol($data, $role_id)
    {
        $rol = Rol::query()->find($role_id);
        return $rol->rolesPersonalizados()
            ->wherePivot('id_obra', '=', $data['id_obra'])
            ->get();
    }

}