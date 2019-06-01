<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\AreaSubcontratante;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\Repositories\Repository;

class AreaSubcontratanteService
{
    protected $repository;

    public function __construct(TipoAreaSubcontratante $model){
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

    public function porUsuario($data, $user_id)
    {
        $usuario = Usuario::query()->find($user_id);
        return $usuario->areasSubcontratantes()
            ->get();
    }

}