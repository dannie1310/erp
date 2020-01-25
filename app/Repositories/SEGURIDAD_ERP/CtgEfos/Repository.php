<?php


namespace App\Repositories\SEGURIDAD_ERP\CtgEfos;

use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use Illuminate\Support\Facades\DB;

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
    public function __construct(CtgEfos $model)
    {
        $this->model = $model;
    }

    public function carga($data)
    {
       $this->model->reg($data);
    }

    public function rfc($data)
    {
       return $this->model->api($data);
    }
}
