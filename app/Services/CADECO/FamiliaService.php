<?php


namespace App\Services\CADECO;


use App\Models\CADECO\Familia;
use App\Repositories\Repository;

class FamiliaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FamiliaService constructor
     *
     * @param Familia $model
     */

    public function __construct(Familia $model)
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
            $familia = $familia->where([['descripcion', 'LIKE', '%' . request('descripcion') . '%']]);
        }
        if(isset($data['tipo_material'])) {
            if (strpos('MATERIAL', strtoupper($data['tipo_material'])) !== FALSE) {
                $familia = $familia->where([['tipo_material', '=', 1]]);
            }
            if (strpos('HERRAMIENTA Y EQUIPO', strtoupper($data['tipo_material'])) !== FALSE) {
                $familia = $familia->where([['tipo_material', '=', 4]]);
            }
        }
            return $familia->paginate($data);

    }

    public function store(array $data)
    {
        $datos = [
            'tipo_material' => $data['tipo'],
            'descripcion' => $data['descripcion']
        ];

        return $this->repository->create($datos);
    }
}
