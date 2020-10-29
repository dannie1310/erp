<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 28/10/2020
 * Time: 06:58 PM
 */

namespace App\Services\CADECO\Contratos;

use App\Repositories\Repository;
use App\Models\CADECO\Subcontratos\TipoContrato;

class TipoContratoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoContratoService constructor.
     * @param TipoContrato $model
     */
    public function __construct(TipoContrato $model)
    {
        $this->repository = new Repository($model);
    }

    public function all($data)
    {
        return $this->repository->all($data);
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
}
