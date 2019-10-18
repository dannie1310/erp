<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 11:55 AM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\AjusteNegativo;
use App\Repositories\CADECO\AjusteNegativo\Repository;

class AjusteNegativoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AjusteNegativoService constructor.
     * @param AjusteNegativo $model
     */
    public function __construct(AjusteNegativo $model)
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