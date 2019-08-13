<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:45 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Contabilidad\Cierre;
use App\Models\CADECO\Cuenta;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class CuentaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaService constructor.
     * @param Cuenta $model
     */
    public function __construct(Cuenta $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $cuenta = $this->repository->create($data);
            DB::connection('cadeco')->commit();
            return $cuenta;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }
}
