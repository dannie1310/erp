<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/19
 * Time: 05:38 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Moneda;
use App\Repositories\CADECO\MonedaRepository;

class MonedaService
{
    /**
     * @var MonedaRepository
     */
    protected $repository;

    /**
     * MonedaService constructor.
     * @param Moneda $model
     */
    public function __construct(Moneda $model)
    {
        $this->repository = new MonedaRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function monedasBase($data)
    {
        return $this->repository->buscarPorBase($data['base']);
    }
}
