<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Tesoreria;


use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use App\Repositories\Repository;

class TraspasoEntreCuentasService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TraspasoEntreCuentasService constructor.
     * @param Repository $repository
     */
    public function __construct(TraspasoCuentas $model)
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

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function store($data)
    {

    }

    public function update($data, $id)
    {

    }
}