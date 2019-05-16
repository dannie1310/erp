<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:00 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Transaccion;
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
        $obra = Obra::query()->find(Context::getIdObra());
        try {
            DB::connection('cadeco')->beginTransaction();
            $antecedente = Transaccion::query()->find($data['id_antecedente']);

            $datos = [
                'id_antecedente' => $data['id_antecedente'],
                'id_obra' => $obra->id_obra,
                'id_empresa' => $antecedente->id_empresa,
                'id_moneda' => $antecedente->id_moneda,
                'cumplimiento' => $data['cumplimiento'],
                'vencimiento' => $data['vencimiento'],
                'monto' => $antecedente->monto,
                'saldo' => $antecedente->saldo,
                'destino' => $antecedente->destino,
                'observaciones' => $data['observaciones'],
                'fecha' => $data['cumplimiento']

            ];

            $solicitud = SolicitudPagoAnticipado::query()->create($datos);

            DB::connection('cadeco')->commit();

            return $solicitud ;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
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