<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/2020
 * Time: 13:27 PM
 */

namespace App\Repositories\CADECO\ControlPresupuesto\ConceptoTarjeta;

use App\Models\CADECO\ControlPresupuesto\ConceptoTarjeta;


class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(ConceptoTarjeta $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function list($id){
        return $this->model->where('id_tarjeta', '=', $id)->get();
    }
    
}