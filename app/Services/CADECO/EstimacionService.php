<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Item;
use App\Models\CADECO\Obra;
use App\PDF\OrdenPagoEstimacion;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class EstimacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EstimacionService constructor.
     */
    public function __construct(Estimacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function find($id)
    {
        return $this->repository->where('id_transaccion', '=', $id);
    }

    public function pdfOrdenPago($id)
    {
        $pdf = new OrdenPagoEstimacion($id);
       return $pdf;
    }

    public function store($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $antes_iva = Obra::query()->find(Context::getIdObra())->datosContables->retencion_antes_iva;

                $estimacion = $this->repository->create($data);

                $pct_anticipo = $estimacion->subcontrato->porcentaje_anticipo;
                $pct_fondo_garantia = $estimacion->subcontrato->porcentaje_fondo_garantia;
                $empresa = $estimacion->subcontrato->empresa;

                $suma_importes = $this->estimaConceptos($estimacion, $data['conceptos']);

                $estimacion->calculaImportes();

                DB::connection('cadeco')->commit();

                return $estimacion;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function estimaConceptos($estimacion, $conceptos)
    {
        $suma = 0;
        foreach ($conceptos as $concepto)
        {
            $suma += $concepto['importe'];

            $pu = Item::query()
                ->where('id_transaccion', '=', $estimacion->id_antecedente)
                ->where('id_concepto', '=', $concepto['item_antecedente'])
                ->first()->precio_unitario;

            Item::query()->create([
                'id_transaccion' => $estimacion->id_transaccion,
                'id_antecedente' => $estimacion->id_antecedente,
                'item_antecedente' => $concepto['item_antecedente'],
                'id_concepto' => $concepto['id_concepto'],
                'cantidad' => $concepto['cantidad'],
                'cantidad_material' => 0,
                'cantidad_mano_obra' => 0,
                'importe' => $concepto['importe'],
                'precio_unitario' => $pu,
                'precio_material' => 0,
                'precio_mano_obra' => 0
            ]);
        }
        return $suma;
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public  function aprobar($id)
    {
        $estimacion = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $estimacion->aprobar();
            DB::connection('cadeco')->commit();
            $estimacion->refresh();
            return $estimacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function revertirAprobacion($id)
    {
        $estimacion = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $estimacion->revertirAprobacion();
            DB::connection('cadeco')->commit();
            $estimacion->refresh();
            return $estimacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }
}