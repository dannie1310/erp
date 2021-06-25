<?php

namespace App\Services\MODULOSSAO\Proyectos;

use App\Models\MODULOSSAO\Proyectos\Empresa;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use App\Models\MODULOSSAO\Proyectos\TipoProyecto;
use App\Repositories\MODULOSSAO\Proyectos\ProyectoRepository as Repository;

class ProyectoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ProyectoService constructor.
     * @param Proyecto $model
     */
    public function __construct(Proyecto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id){
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        if (isset($data['Nombre']))
        {
            $this->repository->where([['Nombre', 'LIKE', '%'.$data['Nombre'].'%']]);
        }

        if (isset($data['empresa']))
        {
            $empresa = Empresa::where([['Empresa', 'LIKE', '%' . $data['empresa'] . '%']])->pluck('IDEmpresa');
            $this->repository->whereIn(['IDEmpresa', $empresa]);
        }

        if (isset($data['tipo']))
        {
            $tipo = TipoProyecto::where([['TipoProyecto', 'LIKE', '%' . $data['tipo'] . '%']])->pluck('IDTipoProyecto');
            $this->repository->whereIn(['IDTipoProyecto', $tipo]);
        }

        if (isset($data['CantidadExtraordinariasPermitidas']))
        {
            $this->repository->where([['CantidadExtraordinariasPermitidas', '=', $data['CantidadExtraordinariasPermitidas']]]);
        }

        return  $this->repository->paginate($data);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }
}
