<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:50 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Apertura;
use App\Models\CADECO\Contabilidad\Cierre;
use App\Repositories\Repository;
use Carbon\Carbon;

class CierreService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CierreService constructor.
     * @param Cierre $model
     */
    public function __construct(Cierre $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update($data, $id)
    {
        if ($data["data"]["estatus"] == 1) { // ABRIR PERIODO
            $apertura = new Apertura;
            $apertura = $apertura->create($data["data"]);
        } else { // CERRAR PERIODO
            $apertura = Apertura::where('id_cierre', '=', $id)->where('estatus', '=', 1)->update(['estatus' => 0, 'fin_apertura' => Carbon::now()->toDateTimeString()]);
        }
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }
}