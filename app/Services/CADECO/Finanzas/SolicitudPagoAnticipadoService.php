<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:00 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class SolicitudPagoAnticipadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudPagoAnticipadoService constructor.
     * @param SolicitudPagoAnticipado $model
     */
    public function __construct(SolicitudPagoAnticipado $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();

            $pago = $this->repository->create($data);

            DB::connection('cadeco')->commit();
            return $pago;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollback();
            dd("AQUI", $e->getMessage());
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
    public function show($id)
    {
        return $this->repository->show($id);
    }
}