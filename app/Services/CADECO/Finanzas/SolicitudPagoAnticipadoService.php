<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:00 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Transaccion;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

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

    public function store(array $data)
    {
        $datos = [
            'id_antecedente' => $data['id_antecedente'],
            'cumplimiento' => $data['cumplimiento'],
            'vencimiento' => $data['vencimiento'],
            'observaciones' => $data['observaciones'],
            'fecha' => $data['cumplimiento'],
            'id_costo' => $data['id_costo']
        ];
        return $this->repository->create($datos);
    }


    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
    public function show($id)
    {
        return $this->repository->show($id);
    }
}