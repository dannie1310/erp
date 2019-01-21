<?php

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use App\Repositories\CADECO\Contabilidad\PolizaMovimientoRepository;
use App\Repositories\CADECO\Contabilidad\PolizaRepository;
use Illuminate\Support\Facades\DB;

class PolizaService
{
    /**
     * @var PolizaRepository
     */
    protected $poliza;

    /**
     * PolizaService constructor.
     * @param PolizaRepository $poliza
     */
    public function __construct(PolizaRepository $poliza)
    {
        $this->poliza = $poliza;
    }

    public function paginate($data)
    {
        $poliza = $this->poliza;

        if(isset($data['startDate'])) {
            $poliza = $poliza->where([['fecha', '>=', $data['startDate']]]);
        }

        if(isset($data['endDate'])) {
            $poliza = $poliza->where([['fecha', '<=', $data['endDate']]]);
        }

        if(isset($data['id_tipo_poliza_contpaq'])) {
            $poliza = $poliza->where([['id_tipo_poliza_contpaq', '=', $data['id_tipo_poliza_contpaq']]]);
        }

        if(isset($data['estatus'])) {
            $poliza = $poliza->where([['estatus', '=', $data['estatus']]]);
        }
        if(isset($data['concepto'])) {
            $poliza = $poliza->where([['concepto', 'LIKE', '%'.$data['concepto'].'%']]);
        }

        return $poliza->paginate($data);
    }

    public function find($id) {
        return $this->poliza->find($id);
    }

    /**
     * @param $data
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function update($data, $id) {

        $data = auth()->user()->can('editar_fecha_prepoliza') ? $data : array_except($data, 'fecha');

        try {
            DB::connection('cadeco')->beginTransaction();
            $poliza = $this->poliza->find($id);

            if(in_array($poliza->estatus,  [1, 2])) {
                throw new \Exception("No se puede modificar la prepóliza ya que su estatus es {$poliza->estatusPrepoliza->descripcion}", 400);
            }

            if(isset($data['fecha'])) {
                $data['fecha_original'] = $poliza->fecha;
            }

            $poliza = $this->poliza->update($data, $id);

            $ids = [];

            foreach ($data['movimientos']['data'] as $movimiento) {
                $movimiento =  auth()->user()->can('editar_importe_movimiento_prepoliza') ? $movimiento : array_except($movimiento, 'importe');

                $movimientoRepository = new PolizaMovimientoRepository(new PolizaMovimiento);
                if (isset($movimiento['id'])) {
                    $movimiento =  auth()->user()->can(['ingresar_cuenta_faltante_movimiento_prepoliza', 'editar_cuenta_contable_movimiento_prepoliza']) ? $movimiento : array_except($movimiento, 'cuenta_contable');
                    $movimientoRepository->update($movimiento, $movimiento['id']);

                    array_push($ids, $movimiento['id']);
                } else {
                    if (auth()->user()->can('agregar_movimiento_prepoliza')) {
                        $movimiento = auth()->user()->can('ingresar_cuenta_faltante_movimiento_prepoliza') ? $movimiento : array_except($movimiento, 'cuenta_contable');
                        $new_movimiento = $poliza->movimientos()->create($movimiento);

                        array_push($ids, $new_movimiento->getKey());
                    }
                }
            }

            if (auth()->user()->can('eliminar_movimiento_prepoliza')) {
                $poliza->movimientos()->whereNotIn('id_int_poliza_movimiento', $ids)->delete();
            }

            $suma_debe = $poliza->movimientos()->whereHas('tipo', function ($query) { return $query->where('id', '=', 1); })->sum('importe');
            $suma_haber = $poliza->movimientos()->whereHas('tipo', function ($query) { return $query->where('id', '=', 2); })->sum('importe');

            $poliza->estatus = 0;
            $poliza->cuadre = $suma_debe - $suma_haber;
            $poliza->total = $suma_debe > $suma_haber ? $suma_debe : $suma_haber;
            $poliza->save();

            DB::connection('cadeco')->commit();
            return $poliza;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function validar($id) {
        try {
            DB::connection('cadeco')->beginTransaction();

            $poliza = $this->poliza->find($id);
            if (! in_array($poliza->estatus, [0, -2])) {
                throw new \Exception("No se puede validar la prepóliza ya que su estatus es {$poliza->estatusPrepoliza->descripcion}", 400);
            }
            $data = ['estatus' => 1];
            $poliza = $this->poliza->update($data, $id);
            $poliza->valido()->create(['valido' => auth()->id()]);

            DB::connection('cadeco')->commit();
            return $poliza;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function omitir($id) {
        try {
            DB::connection('cadeco')->beginTransaction();

            $data = [
                'estatus' => -3,
                'lanzable' => true
            ];

            $poliza = $this->poliza->update($data, $id);

            DB::connection('cadeco')->commit();
            return $poliza;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort($e->getCode(), $e->getMessage());
        }
    }
}