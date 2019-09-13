<?php


namespace App\Services\CADECO\Almacenes;

use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Repositories\Repository;
use PhpParser\Node\Stmt\Return_;

class InventarioFisicoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * InventarioFisicoService constructor.
     * @param InventarioFisico $model
     */
    public function __construct(InventarioFisico $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $inventario = $this->repository->create($data);
        return $this->repository->show($inventario->id);
    }

    public function actualizar($id){
        $dato = ['estado' => 1];
        $this->repository->show($id)->update($dato);
        return $this->repository->show($id);
    }

    public function generar_marbetes($id){
        return $this->repository->show($id)->pdf_marbetes();
    }

    public function descargaLayout($id)
    {
        return $this->repository->show($id)->descargaLayout();
    }

}
