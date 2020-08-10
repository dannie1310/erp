<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\EmpresaRepository as Repository;

class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EmpresaService constructor.
     * @param Empresa $model
     */
    public function __construct(Empresa $model)
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

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        return $this->repository->store($data);
    }

    public function getDoctosGenerales($id){
        dd('pandita', $this->repository->show($id)->archivos);
    }
}
