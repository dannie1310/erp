<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Credito;
use App\Models\CADECO\Debito;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use App\Models\CADECO\Tesoreria\TraspasoTransaccion;
use App\Models\CADECO\Transaccion;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class TraspasoEntreCuentasService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TraspasoEntreCuentasService constructor.
     * @param Repository $repository
     */
    public function __construct(TraspasoCuentas $model)
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

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function store($data)
    {
        $obra = Obra::query()->find(Context::getIdObra());
        $id_moneda = $obra->id_moneda;

        try {
            DB::connection('cadeco')->beginTransaction();
            $record = $this->repository->create($data);

            $credito = [
                'tipo_transaccion' => 83,
                'fecha' => $data['fecha'] ? $data['fecha'] : date('Y-m-d'),
                'estado' => 1,
                'id_obra' => $obra->id_obra,
                'id_cuenta' => $data['id_cuenta_destino'],
                'id_moneda' => $id_moneda,
                'cumplimiento' => $data['cumplimiento'] ? $data['cumplimiento'] : date('Y-m-d'),
                'vencimiento' => $data['cumplimiento'] ? $data['cumplimiento'] : date('Y-m-d'),
                'opciones' => 1,
                'monto' => $data['importe'],
                'impuesto' => 0,
                'referencia' => $data['referencia'],
                'comentario' => "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario,
                'observaciones' => $data['observaciones'],
                'FechaHoraRegistro' => date('Y-m-d h:i:s'),
            ];

            $debito = $credito;
            $debito['tipo_transaccion'] = 84;
            $debito['id_cuenta'] = $data['id_cuenta_origen'];
            $debito['monto'] = (float)  ($data['importe'] * -1);

            // Crear transaccion Débito
            $transaccion_debito = Debito::query()->create($debito);

            // Crear transaccion Crédito
            $transaccion_credito = Credito::query()->create($credito);

            // Revisa si la transacción se realizó
            $debito_realizo = Transaccion::query()->where('id_transaccion', $transaccion_debito->id_transaccion)->first();
            $credito_realizo = Transaccion::query()->where('id_transaccion', $transaccion_credito->id_transaccion)->first();

            // Si alguna de las transacciones no se registró, regresa un error
            if (!$debito_realizo || !$credito_realizo)
            {
                throw new \Exception("El traspaso no se pudo concretar", 400);
            }

            // Enlaza las transacciones con su respectivo traspaso. Debito
            TraspasoTransaccion::query()->create([
                'id_traspaso' => $record->id_traspaso,
                'id_transaccion' => $transaccion_debito->id_transaccion,
                'tipo_transaccion' => $debito['tipo_transaccion'],
            ]);

            // Enlaza las transacciones con su respectivo traspaso. Credito
            TraspasoTransaccion::query()->create([
                'id_traspaso' => $record->id_traspaso,
                'id_transaccion' => $transaccion_credito->id_transaccion,
                'tipo_transaccion' => $credito['tipo_transaccion'],
            ]);

            DB::connection('cadeco')->commit();

            return $record;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function update($data, $id)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $item = $this->repository->update($data, $id);
            foreach ($item->transacciones as $transaccion) {
                //Debito
                if ($transaccion->tipo_transaccion == 84) {
                    $debito = Debito::query()->find($transaccion->id_transaccion);
                    $debito->referencia = $data['referencia'];
                    $debito->fecha = $data['fecha'];
                    $debito->cumplimiento = $data['cumplimiento'];
                    $debito->vencimiento = $data['cumplimiento'];
                    $debito->monto = (float)  ($data['importe'] * -1);
                    $debito->id_cuenta = $data['id_cuenta_origen'];
                    $debito->observaciones = $data['observaciones'];
                    $debito->save();
                    //Crédito
                } else if ($transaccion->tipo_transaccion == 83) {
                    $credito = Credito::query()->find($transaccion->id_transaccion);
                    $credito->referencia = $data['referencia'];
                    $credito->fecha = $data['fecha'];
                    $credito->cumplimiento = $data['cumplimiento'];
                    $credito->vencimiento = $data['cumplimiento'];
                    $credito->monto = $data['importe'];
                    $credito->id_cuenta = $data['id_cuenta_destino'];
                    $credito->observaciones = $data['observaciones'];
                    $credito->save();
                }
            }
            DB::connection('cadeco')->commit();

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort($e->getCode(), $e->getMessage());
        }
        return $item;
    }

    public function delete($data, $id)
    {
        try {
            DB::connection('cadeco')->beginTransaction();

            $this->repository->delete($data, $id);

            // Obtener el id de las transacciones a eliminar
            $transacciones = TraspasoTransaccion::where('id_traspaso', '=', $id)->get();

            foreach ($transacciones as $tr)
            {
                //Debito
                if ($tr->tipo_transaccion == 84) {
                    $debito = Debito::query()->find($tr->id_transaccion);
                    $debito->estado = -2;
                    $debito->save();

                    //Crédito
                } else if ($tr->tipo_transaccion == 83) {
                    $credito = Credito::query()->find($tr->id_transaccion);
                    $credito->estado = -2;
                    $credito->save();
                }
            }

            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort($e->getCode(), $e->getMessage());
        }

        return true;
    }
}