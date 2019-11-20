<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Requisicion;
use App\Repositories\CADECO\Compras\Requisicion\Repository;

class RequisicionService
{
    /**
     * @var Repository
     */
    protected $repsitory;

    /**
     * RequisicionService constructor.
     * @param Requisicion $model
     */
    public function __construct(Requisicion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $datos = [
            'id_area_compradora' => $data['id_area_compradora'],
            'id_tipo' => $data['id_tipo'],
            'id_area_solicitante' => $data['id_area_solicitante'],
            'concepto' => $data['concepto'],
            'partidas' => $data['partidas'],
            'observaciones'=> $data['observaciones']
        ];
        return $this->repository->create($datos);
    }
}