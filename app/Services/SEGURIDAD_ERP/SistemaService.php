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
//        $bases =  Context::getDatabase();
//        $id_bases = Proyecto::query()->where('base_datos', '=', $bases)->get();
//
//        dd($id_bases);
//        $obra = Context::getIdObra();


        $sistema = Proyecto::query()->find(10);
        return $sistema->sistemas()->where('id_obra','=',Context::getIdObra())
            ->get();
//        dd($usuario);

//        return $query
//            ->whereHas('permisos', function($q) use ($permisos) {
//                return $q->whereIn('name', $permisos);
//            })
//            ->whereHas('proyectos', function ($q) {
//                return $q->where('base_datos', '=', Context::getDatabase())->where('id_obra','=',Context::getIdObra());
//            });
    }

}