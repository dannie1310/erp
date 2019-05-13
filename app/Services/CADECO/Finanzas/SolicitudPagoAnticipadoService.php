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
      /*  $data['id_usuario'] = $this->id_usuario;
        $data['usuario_registra'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;*/
        return $this->repository->create($data);
    }
}