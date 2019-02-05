<?php
/**
 * Created by PhpStorm.
 * User: emartinez
 * Date: 05/02/19
 * Time: 12:53 PM
 */

namespace App\Repositories\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use App\Traits\RepositoryTrait;

class SolicitudMovimientoRepository
{
    use RepositoryTrait;

    /**
     * @var SolicitudMovimientoFondoGarantia
     */
    private $model;

    /**
     * SolicitudMovimientoFondoGarantiaRepository constructor.
     * @param SolicitudMovimientoFondoGarantia $model
     */
    public function __construct(SolicitudMovimientoFondoGarantia $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}