<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 04/06/2020
 * Time: 02:16 PM
 */

namespace App\Services\CADECO\Compras;

use App\Repositories\Repository;
use App\Models\CADECO\Compras\CtgFormaPagoCredito;


class FormaPagoCreditoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(CtgFormaPagoCredito $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}