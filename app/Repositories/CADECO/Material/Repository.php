<?php


namespace App\Repositories\CADECO\Material;

use App\Facades\Context;
use App\Models\CADECO\Material;
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
    public function __construct(Material $model)
    {
        $this->model = $model;
    }

    
    public function list($data)
    {
       return $this->model->lista_materiales($data);
    }
}
