<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\Sistema;
use App\Repositories\Repository;

class SistemaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SistemaService constructor.
     * @param Sistema $model
     */
    public function __construct(Sistema $model)
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

    public function porObra()
    {
        $sistema = Proyecto::query()->find(10);
        return $sistema->sistemas()->where('id_obra','=',Context::getIdObra())
            ->get();
    }
    public function asignacionSistemas($data)
    {
        $proyecto = Proyecto::query()->find(10);
//        dd($proyecto);
//
        foreach ($data['sistema_id'] as $sist) {
            $sistemas = Sistema::find($sist);
////
////            if($permiso->reservado &&  ! auth()->user()->can('asignar_permisos_reservados')) {
////                throw new \Exception('No es posible asignar el permiso "' . $permiso->display_name. '" porque se trata de un permiso reservado, favor de solicitar la asignación al administrador del sistema.', 403);
////            }
        }
//        dd($sistemas);
        $sistemas_originales = $proyecto->sistemas()->pluck('id_sistema')->toArray();
        dd($sistemas_originales);

//
//        foreach ($data['permission_id'] as $id) {
//            // ASIGNACIÓN
//            if (! in_array($id, $permisos_originales)) {
//                \App\Models\CADECO\Seguridad\AuditoriaPermisoRol::query()->create([
//                    'role_id' => $data['role_id'],
//                    'permission_id' => $id,
//                    'action' => 'Registro'
//                ]);
//            }
//        }
//
        $proyecto->sistemas()->detach($proyecto->sistemas()->pluck('id_sistema')->toArray());
        $proyecto->sistemas()->sync($data['sistema_id'], false);
//
        $sistemas_actualizados = $proyecto->sistemas()->pluck('id_sistema')->toArray();
//        dd($sistemas_actualizados);
//        foreach ($permisos_originales as $id) {
//            // DESASIGNACIÓN
//            if (! in_array($id, $permisos_actualizados)) {
//                \App\Models\CADECO\Seguridad\AuditoriaPermisoRol::query()->create([
//                    'role_id' => $data['role_id'],
//                    'permission_id' => $id,
//                    'action' => 'Eliminación'
//                ]);
//            }
//        }
        return true;
    }

}