<?php


namespace App\Repositories\CADECO\Unidad;

use App\Facades\Context;
use App\Models\CADECO\Unidad;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;


    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Unidad $model)
    {
        $this->model = $model;
    }

    public function buscar($data)
    {
       return $this->model->where('unidad', '=',$data)->first();
    }

    public function buscarPorBase($data)
    {
        return $this->model->buscarPorBase($data);
    }
}
