<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 07/02/19
 * Time: 04:45 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\Material;
use App\Repositories\Repository;

class AlmacenService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AlmacenService constructor.
     *
     * @param Almacen $model
     */
    public function __construct(Almacen $model)
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

    public function materialesAlmacen($id)
    {
    return  Material::query()->join('inventarios','inventarios.id_material', 'materiales.id_material')
          ->where('inventarios.id_almacen','=', $id)->whereIn('materiales.tipo_material', array(1,4))
          ->orderBy('materiales.descripcion','asc')->select('materiales.*')->distinct()->get()->toArray();
    }
}
