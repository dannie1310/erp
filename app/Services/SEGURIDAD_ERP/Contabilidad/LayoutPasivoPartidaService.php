<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartidaRepository;


class LayoutPasivoPartidaService{

    /**
     * @var LayoutPasivoPartidaRepository
     */
    protected $repository;

    /**
     * @param LayoutPasivoPartida $model
     */
    public function __construct(LayoutPasivoPartida $model)
    {
        $this->repository = new LayoutPasivoPartidaRepository($model);
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
}
