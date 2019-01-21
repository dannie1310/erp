<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:18 PM
 */

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaGeneral;
use App\Traits\RepositoryTrait;

class CuentaGeneralRepository
{
    use RepositoryTrait;

    /**
     * @var CuentaGeneral
     */
    private $model;

    /**
     * CuentaGeneralRepository constructor.
     * @param CuentaGeneral $model
     */
    public function __construct(CuentaGeneral $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

}