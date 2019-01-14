<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 04:47 PM
 */

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaFondo;
use App\Traits\RepositoryTrait;

class CuentaFondoRepository
{
    use RepositoryTrait;

    /**
     * @var CuentaFondo
     */
    private $model;

    /**
     * CuentaFondoRepository constructor.
     * @param CuentaFondo $model
     */
    public function __construct(CuentaFondo $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}