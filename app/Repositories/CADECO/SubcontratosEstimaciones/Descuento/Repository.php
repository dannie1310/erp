<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/2020
 * Time: 13:27 PM
 */

namespace App\Repositories\CADECO\SubcontratosEstimaciones\Descuento;

use App\Models\CADECO\SubcontratosEstimaciones\Descuento;

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
    public function __construct(Descuento $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function list($id){
        $this->sort();
        return $this->model->where('id_transaccion', '=', $id)->get();
    }
}