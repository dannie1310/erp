<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 05:00 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Material;
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
     * @param Material $model
     */
    public function __construct(Material $model)
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
        $material = $this->repository;

        if(isset($data['descripcion'])) {
            $salida = $material->where([['descripcion', 'LIKE', '%' . request('descripcion') . '%']]);
        }
        return $material->paginate($data);
    }
    public function store(array $data)
    {
        $datos = [
            'nivel' => $data['tipo'],
            'unidad' => $data['unidad'],
            'descripcion' => $data['descripcion'],
            'numero_parte' => $data['nu_parte'],
            'tipo_material' => $data['tipo_material'],
            'equivalencia' => $data['equivalencia'],
            'marca' => $data['marca']

        ];

        return $this->repository->create($datos);
    }
}
