<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:43 PM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\AjustePositivo;
use App\Repositories\CADECO\AjustePositivo\Repository;

class AjustePositivoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(AjustePositivo $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {
        $datos = [
            'id_almacen' => $data['id_almacen'],
            'referencia' => $data['referencia'],
            'observaciones' => $data['observaciones'],
            'items' =>  $data['items']
        ];

        return $this->repository->create($datos);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}