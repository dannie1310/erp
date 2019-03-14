<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Estimacion;
use App\PDF\Formato\OrdenPagoEstimacion;
use App\Repositories\Repository;

class EstimacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EstimacionService constructor.
     */
    public function __construct(Estimacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function find($id)
    {
        return $this->repository->where('id_transaccion', '=', $id);
    }

    public function pdf($id)
    {
        //$pdf = $this->repository->where('id_transaccion', '=', $id);
        $pdf = new OrdenPagoEstimacion($id);
        $pdf->create()->Output('D', 'Formato - Orden Pago Estimacion.pdf', 1);
       return $pdf;
    }
}