<?php


namespace App\Repositories\CADECO\Compras\Asignacion;


use App\Repositories\RepositoryInterface;
use App\Models\CADECO\Compras\AsignacionProveedor;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    public function __construct(AsignacionProveedor $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function paginate()
    {
        $this->search();
        $this->scope();
        $this->sort();
        $query = $this->model;

        if (request('limit') && request('offset') != '') {
            return $query->paginate(request('limit'), ['*'], 'page', (request('offset') / request('limit')) + 1);
        }

        return $query->paginate(10);
    }


    public function sort()
    {
        if (request('sort')) {
            if(request('sort') == 'registro'){
                $this->model = $this->model->orderBy('timestamp_registro', request('order'));
            }else{
                $this->model = $this->model->orderBy(request('sort'), request('order'));
            }

        }
    }
}
