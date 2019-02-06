<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Tesoreria;


use App\Models\CADECO\Tesoreria\MovimientoBancario;
use App\Repositories\Repository;

class MovimientoBancarioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MovimientoBancarioService constructor.
     * @param MovimientoBancario $model
     */
    public function __construct(MovimientoBancario $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }
}