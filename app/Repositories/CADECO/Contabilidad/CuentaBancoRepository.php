<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/02/2019
 * Time: 04:20 PM
 */

namespace App\Repositories\CADECO\Contabilidad;

use App\Models\CADECO\Contabilidad\CuentaBanco;
use App\Traits\RepositoryTrait;

class CuentaBancoRepository
{
    use RepositoryTrait;

    /**
     * @var CuentaBanco
     */
    private $model;

    /**
     * CuentaBancoTransformer constructor.
     * @param CuentaBanco $model
     */
    public function __construct(CuentaBanco $model)
    {
        $this->model = $model;
    }


    public function create(array $data)
    {
        return $this->model->create($data);
    }

}
