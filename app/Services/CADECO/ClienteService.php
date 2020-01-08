<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/01/2020
 * Time: 01:44 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Cliente;
use App\Repositories\CADECO\Cliente\Repository;

class ClienteService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Cliente $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $cliente = $this->repository;

        if(isset($data['rfc']))
        {
            $cliente = $cliente->where([['rfc', 'LIKE', '%' . request('rfc') . '%']]);
        }
        if(isset($data['razon_social']))
        {
            $cliente = $cliente->where([['razon_social', 'LIKE', '%' . request('razon_social') . '%']]);
        }

        return $cliente->paginate($data);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }
}