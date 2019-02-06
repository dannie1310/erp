<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:12 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\EstatusPrepoliza;
use App\Repositories\Repository;

class EstatusPrepolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EstatusPrepolizaService constructor.
     * @param EstatusPrepoliza $model
     */
    public function __construct(EstatusPrepoliza $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}