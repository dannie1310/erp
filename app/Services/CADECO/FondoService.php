<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 12:59 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Fondo;
use App\Repositories\CADECO\Fondo\Reporsitory;

class FondoService
{
    /**
     * @var Reporsitory
     */
    protected $repository;

    /**
     * FondoService constructor.
     *
     * @param Fondo $model
     */
    public function __construct(Fondo $model)
    {
        $this->repository = new Reporsitory($model);
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        $fondo = $this->repository;
        if (isset($data['cuenta__cuenta'])) {
            $fondo = $fondo->where([['cuenta.cuenta', 'LIKE', '%' . $data['cuenta__cuenta'] . '%']]);
        }

        if (isset($data['id_fondo'])) {
            $fondo= $fondo->where([['fondos.descripcion', 'LIKE', '%' . $data['id_fondo'] . '%']]);
        }
        return $fondo->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index()
    {
        return $this->repository->all();
    }

}