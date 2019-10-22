<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:20 PM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\NuevoLote;
use App\Repositories\CADECO\NuevoLote\Repository;

class NuevoLoteService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * NuevoLoteService constructor.
     * @param NuevoLote $model
     */
    public function __construct(NuevoLote $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
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

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data'][0]);
    }
}