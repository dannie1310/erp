<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 07/02/19
 * Time: 04:45 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Almacen;
use App\Repositories\CADECO\Almacen\Repository;

class AlmacenService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AlmacenService constructor.
     *
     * @param Almacen $model
     */
    public function __construct(Almacen $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        if(is_null($this->repository->findAlmacen($data)))
        {
            $data['descripcion'] = strtoupper($data['descripcion']);
            return $this->repository->create(array_except($data,'tipos_almacenes'));
        }
        abort(400, "El almacén '". $data['descripcion']."' se encuentra registrado.");
    }

    public function update(array $data, $id)
    {
        $almacen = $this->repository->findAlmacen($data);
        if(is_null($almacen) || $almacen->id_almacen == $id)
        {
            $data['descripcion'] = strtoupper($data['descripcion']);
            return $this->repository->update($data, $id);
        }
        abort(400, "La descripción del almacén '". $data['descripcion']."' ya esta registrado previamente.");
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar();
    }
}
