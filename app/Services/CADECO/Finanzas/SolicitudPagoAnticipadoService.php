<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:00 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Repositories\Repository;

class SolicitudPagoAnticipadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudPagoAnticipadoService constructor.
     * @param SolicitudPagoAnticipado $model
     */
    public function __construct(SolicitudPagoAnticipado $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data){

        dd($data);

     /*   $data['monto'] = $this->transaccion->monto;
        $data['saldo'] = $this->transaccion->saldo;
        $data['id_empresa'] = $this->transaccion->empresa->id_empresa;
        $data['id_moneda'] = $this->transaccion->id_moneda;
        $data['destino'] = $this->transaccion->destino;*/
        dd($data);
        return $this->repository->create($data);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}