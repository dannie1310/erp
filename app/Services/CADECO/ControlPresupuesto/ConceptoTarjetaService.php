<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:45 PM
 */

namespace App\Services\CADECO\ControlPresupuesto;

use App\Repositories\CADECO\ControlPresupuesto\ConceptoTarjeta\Repository;
use App\Models\CADECO\ControlPresupuesto\ConceptoTarjeta;

class ConceptoTarjetaService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ConceptoTarjeta constructor.
     *
     * @param ConceptoTarjeta $model
     */
    public function __construct(ConceptoTarjeta $model)
    {
        $this->repository = new Repository($model);
    }
    
    public function list($id)
    {
        return $this->repository->list($id);
    }
    
}