<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 05:11 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\FinanzasCBE\Solicitud;
use App\Repositories\Repository;

class SolicitudCuentaBancariaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCuentaBancariaService constructor.
     * @param Solicitud $model
     */
    public function __construct(Solicitud $model)
    {
        $this->repository = new Repository($model);
    }
}