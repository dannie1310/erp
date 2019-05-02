<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 11:13 AM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaAlmacen;
use App\Repositories\CADECO\CuentaAlmacen\Repository;

class CuentaAlmacenService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaAlmacenService constructor.
     * @param CuentaAlmacen $model
     */
    public function __construct(CuentaAlmacen $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $fondo = $this->repository;
        if (isset($data['almacen__tipo_almacen'])) {
            $fondo = $fondo->where([['almacen.tipo_almacen', 'LIKE', '%' . $data['almacen__tipo_almacen'] . '%']]);
        }

        if (isset($data['id_almacen'])) {
            $fondo= $fondo->where([['almacen.id_almacen', 'LIKE', '%' . $data['id_almacen'] . '%']]);
        }

        if (isset($data['cuentas_almacenes__cuenta'])) {
            $fondo = $fondo->where([['cuentas_almacenes.cuenta', 'LIKE', '%' . $data['cuentas_almacenes__cuenta'] . '%']]);
        }

        return $fondo->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }
}