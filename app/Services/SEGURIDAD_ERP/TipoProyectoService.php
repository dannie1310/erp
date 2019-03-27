<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/21/19
 * Time: 5:35 PM
 */

namespace App\Services\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\TipoProyecto;
use App\Repositories\Repository;

class TipoProyectoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoProyectoService constructor.
     * @param TipoProyecto $model
     */
    public function __construct(TipoProyecto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}