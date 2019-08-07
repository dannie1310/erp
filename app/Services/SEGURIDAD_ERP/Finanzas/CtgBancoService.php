<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/08/2019
 * Time: 12:00 PM
 */


namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\Models\CADECO\Empresa;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgBanco;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class CtgBancoService
{

    /**
     * @var Repository
     */
    protected $repository;


    /**
     * CtgBancoService constructor
     * @param CtgBanco $model
     */

    public function __construct(CtgBanco $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

}
