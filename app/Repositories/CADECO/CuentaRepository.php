<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:46 PM
 */

namespace App\Repositories\CADECO;


use App\Models\CADECO\Cuenta;
use App\Traits\RepositoryTrait;

class CuentaRepository
{
    use RepositoryTrait;

    /**
     * @var Cuenta
     */
    protected $model;

    /**
     * CuentaRepository constructor.
     * @param Cuenta $model
     */
    public function __construct(Cuenta $model)
    {
        $this->model = $model;
    }
}