<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
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
                } catch (\Exception $e) {}
            }
        }

        return true;
    }
}