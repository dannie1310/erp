<?php


namespace App\Repositories\CADECO\Empresa;

use App\Facades\Context;
use App\Models\CADECO\Empresa;
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
    public function __construct(Empresa $model)
    {
        $this->model = $model;
    }

    public function getDuplicados($rfc){
        return $this->model->where('rfc', '=', $rfc)->orderBy('id_empresa', 'ASC')->get();
    }

}
