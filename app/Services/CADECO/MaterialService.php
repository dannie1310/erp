<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 05:00 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Material;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

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

    public function porInventario($data)
    {
        $query = DB::select('SELECT m.id_material , m.descripcion , sum(saldo) as saldo, m.numero_parte ,m.unidad
                    FROM '.Context::getDatabase().'.[dbo].[inventarios] i
                    inner join '.Context::getDatabase().'.[dbo].materiales m on i.id_material = m.id_material
                    where i.id_almacen = '.$data['almacen'].' and saldo > 0 and m.numero_parte is not null
                    group by  m.id_material ,m.id_material,m.descripcion,m.numero_parte,m.unidad
                    order by m.descripcion');

        return $query;
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}
