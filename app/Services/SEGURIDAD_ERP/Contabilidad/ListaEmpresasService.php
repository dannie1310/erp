<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Repositories\SEGURIDAD_ERP\Contabilidad\ListaEmpresaRepository as Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;

class ListaEmpresasService{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * IncidenciaService constructor.
     * @param Incidencia $model
     */
    public function __construct(Empresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function consolida($data, $id)
    {
        $this->repository->show($id)->actualizaEmpresas($data['params']);
    }

    public function sincronizar()
    {
        return $this->repository->sincronizar();
    }

    public function actualizaAccesoMetadatos()
    {
        $this->repository->actualizaAccesoMetadatos();
    }

    public function validaCuenta($id_empresa, $cuenta)
    {


    }
}
