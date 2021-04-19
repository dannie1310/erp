<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Credito;
use App\Models\CADECO\Debito;
use App\Models\CADECO\Tesoreria\MovimientoBancario;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class MovimientoBancarioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MovimientoBancarioService constructor.
     * @param MovimientoBancario $model
     */
    public function __construct(MovimientoBancario $model)
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

    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function store($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();

            $data['impuesto'] = isset($data['impuesto']) ? $data['impuesto'] : 0;
            $movimiento = $this->repository->create($data);

            $transaccionRepository = new Repository($movimiento->tipo->naturaleza == 1 ? new Credito : new Debito);

            $transaccion = $transaccionRepository->create($data);
            $transaccion->monto = $movimiento->importe + $movimiento->impuesto;
            $transaccion->impuesto = $movimiento->impuesto;
            $transaccion->save();

            $movimiento->transacciones()->attach($transaccion);

            DB::connection('cadeco')->commit();

            return $movimiento;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function update($data, $id)
    {
        try {
            DB::connection('cadeco')->beginTransaction();

            if ($data['id_tipo_movimiento'] != 4) {
                $data['impuesto'] = 0;
            }

            $movimiento = $this->repository->update($data,  $id);


            $transaccionRepository = new Repository($movimiento->tipo->naturaleza == 1 ? new Credito : new Debito);

            $data['monto'] = $movimiento->importe + $movimiento->impuesto;
            $data['impuesto'] = $movimiento->impuesto;

            if($transaccion = $transaccionRepository->show($movimiento->transacciones()->first()->getKey())) {
                $transaccionRepository->update($data, $movimiento->transacciones()->first()->getKey());
            } else {
                $transaccionRepository->create($data);
            }

            DB::connection('cadeco')->commit();

            return $movimiento;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }
}
