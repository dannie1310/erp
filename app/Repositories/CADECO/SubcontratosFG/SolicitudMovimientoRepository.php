<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 12:06 PM
 */

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaAlmacen;
use App\Traits\RepositoryTrait;

class CuentaAlmacenRepository
{
    use RepositoryTrait;

    /**
     * @var CuentaAlmacen
     */
    private $model;

    /**
     * CuentaAlmacenRepository constructor.
     * @param CuentaAlmacen $model
     */
    public function __construct(CuentaAlmacen $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}