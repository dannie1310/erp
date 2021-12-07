<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
use Illuminate\Support\Facades\DB;
use App\PDF\Contratos\EstimacionFormato;
use App\PDF\Contratos\OrdenPagoEstimacion;
use App\PDF\PortalProveedores\SolicitudAvanceFormato;
use App\Repositories\CADECO\EstimacionRepository as Repository;

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
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio'])){
            $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['monto'])){
            $this->repository->where([['monto', 'LIKE', '%'.$data['monto'].'%']]);
        }

        if(isset($data['numero_folio_sub'])){
            $subcontratos = Subcontrato::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio_sub'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente',  $subcontratos]);
        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 0]]);
            }
            else if (strpos('APROBADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 1]]);
            }else if (strpos('REVISADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 2]]);
            }
        }

        if(isset($data['referencia_sub'])){
            $contrato_proyectado = Subcontrato::query()->where([['referencia', 'LIKE', '%'.$data['referencia_sub'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente',  $contrato_proyectado]);
        }

        if(isset($data['contratista'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['contratista'].'%']])->pluck("id_empresa");
            $this->repository->whereIn(['id_empresa', $empresa]);
        }

        if(isset($data['consecutivo'])){
            $estimaciones = \App\Models\CADECO\SubcontratosEstimaciones\Estimacion::query()->where([['NumeroFolioConsecutivo', 'LIKE', '%'.$data['consecutivo'].'%']])->pluck("IDEstimacion");
            $this->repository->whereIn(['id_transaccion',  $estimaciones]);
        }
        return $this->repository->paginate($data);
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

    public function indexProveedor()
    {
        return $this->repository->estimacionesProveedor();
    }

    public function storeProveedor($data)
    {
        try {
            return $this->repository->createProveedor($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function proveedorConceptos($id, $base)
    {
        return $this->repository->subcontratoAEstimar($id, $base);
    }

    public function updateProveedor(array $data, $id)
    {
        return $this->repository->updateProveedor($data, $id);
    }

    public function deleteProveedor($data, $id)
    {
        return $this->repository->eliminar($id, $data['data']);
    }

    public function pdfSolicitudAvanceFormato($id, $data){
        $pdf = new SolicitudAvanceFormato($id, $data['db']);
        return $pdf;
    }
}
