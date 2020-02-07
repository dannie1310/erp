<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/08/2019
 * Time: 12:00 PM
 */


namespace App\Services\CADECO;


use App\Models\CADECO\Unidad;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class UnidadService
{

    /**
     * @var Repository
     */
    protected $repository;


    /**
     * UnidadService constructor
     * @param Unidad $model
     */

    public function __construct(Unidad $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
    public function show($id)
    {
        dd('id', $id);
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        if(isset($data['descripcion']))
        {
            return $this->repository->where([['descripcion','like', '%'.$data['descripcion'].'%']])->paginate();
        }
        return $this->repository->paginate();
    }

    public function store(array $data)
    {
        $datos = [
            'unidad' => $data['unidad'],
            'descripcion' => $data['descripcion']
        ];
        
        return $this->repository->create($datos);
    }

}
