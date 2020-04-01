<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO;


use App\Repositories\CADECO\EstimacionRepository as Repository;
use App\Models\CADECO\Estimacion;
use Illuminate\Support\Facades\DB;
use App\PDF\Contratos\EstimacionFormato;
use App\PDF\Contratos\OrdenPagoEstimacion;

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

    public function pdfOrdenPago($id)
    {
        $pdf = new OrdenPagoEstimacion($id);
       return $pdf;
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        $estimaciones = $this->repository;

        if(isset($data['numero_folio'])){
        $estimaciones = $estimaciones->where([['numero_folio','=',$data['numero_folio']]]);
        }

        return $estimaciones->paginate($data);
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

    public  function anticipo($data, $id)
    {
        $estimacion = $this->repository->show($id);
        return $estimacion->anticipoAmortizacion($data['campo']);
    }

    public function revertirAprobacion($id)
    {
        $estimacion = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $estimacion->revertirAprobacion();
            $estimacion->cancelarRetencion();
            DB::connection('cadeco')->commit();
            $estimacion->refresh();

            return $estimacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function pdfEstimacion($id)
    {
        $pdf = new EstimacionFormato($id);
        return $pdf;
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }

    public function registrarRetencionIva($data, $id)
    {
        $estimacion = $this->repository->show($id);
        return $estimacion->registrarIVARetenido($data);
    }

    public function ordenado($id)
    {
        return $this->show($id)->subcontratoAEstimar();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }
}
