<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 05:00 PM
 */

namespace App\Services\CADECO\Compras;


use App\Models\CADECO\MaterialFamilia;
use App\Repositories\Repository;

class MaterialService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MaterialService constructor.
     *
     * @param MaterialFamilia $model
     */
    public function __construct(MaterialFamilia $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        $familia = $this->repository;

        if(isset($data['descripcion'])) {
            $salida = $familia->where([['descripcion', 'LIKE', '%' . request('descripcion') . '%']]);
        }
        return $familia->paginate($data);
    }
    public function store(array $data)
    {
        $datos = [
            'tipo' => $data['tipo'],
            'unidad' => $data['unidad'],
            'descripcion' => $data['descripcion'],
            'nu_parte' => $data['nu_parte']
        ];

        return $this->repository->create($datos);
    }
}
