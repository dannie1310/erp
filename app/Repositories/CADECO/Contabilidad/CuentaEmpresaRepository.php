<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:29 PM
 */

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use App\Traits\RepositoryTrait;

class CuentaEmpresaRepository
{
    use RepositoryTrait;

    /**
     * @var CuentaEmpresa
     */
    private $model;

    /**
     * CuentaEmpresaRepository constructor.
     * @param CuentaEmpresa $model
     */
    public function __construct(CuentaEmpresa $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}